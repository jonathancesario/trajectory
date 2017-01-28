<?php namespace App\Http\Controllers;

use App\Exceptions\DataException;
use App\ProjectionGenerator;
use App;
use Excel;
use Exception;
use Input;
use Request;
use Khill\Lavacharts\Lavacharts;


class TrajectoryController extends Controller
{
    private $generator;

    public function __construct()
    {
        $this->generator = App::make(ProjectionGenerator::class);
    }

    public function show()
    {
        if (Request::method() == 'GET')
            return view('content');

        $action = Input::get('action');
        $method = Input::get('method');
        if ($action == 'export') {
            $sample = $this->getSampleData();
            Excel::create('input-sample', function ($excel) use ($sample) {
                $excel->sheet('sheet', function ($sheet) use ($sample) {
                    $sheet->row(1, ['md', 'inc', 'azimuth', 'tvd', 'north', 'east', 'hd']);
                    for($i = 2; $i <= count($sample); $i++){
                        $sheet->row($i, $sample[$i-2]);
                    }
                });
            })->export('xlsx');
        } else if ($action == 'import') {
            if (Input::hasFile('data')) {
    			$path = Input::file('data')->getRealPath();
                $input = Excel::load($path, function($reader) {
                    $reader->ignoreEmpty();
                })->get()->toArray();

                try {
                    $actualInput = $this->validateInput($input);
                } catch (Exception $exc) {
                    return view('content')->with(['alert' => $exc->getMessage()]);
                }

                list($verticalPoints, $northEastPoints, $input) = call_user_func_array([$this->generator, $method], [$input, $actualInput]);

                $table = $this->generateTable($input);

                $chart = App::make(Lavacharts::class);
                $this->generateChart($chart, $verticalPoints, 'Vertical', -1, $this->getMethodName($method));
                $this->generateChart($chart, $northEastPoints, 'NorthEast', 1, $this->getMethodName($method));

                return view('content', compact('chart'))->with(['table' => $table]);
    		}
            return view('content');
        }
    }

    private function generateChart($chart, $points, $name, $direction, $method)
    {
        /* initialize and setup charts */
        $trajectory = $chart->DataTable();
        $trajectory
            ->addNumberColumn('Horizontal Departures')
            ->addNumberColumn($method)
            ->addNumberColumn('Actual');

        /* calculate input and add data to charts */
        foreach ($points as $row) {
            $trajectory->addRow([$row[0], $row[1], $row[2]]);
        }

        /* generate the chart */
        $depth = max(end($points)[1], end($points)[2]);
        $ticks = $this->getTicks($depth);
        $chart->LineChart($name, $trajectory, [
            'title' => $method,
            'vAxis' => [
                'direction' => $direction,
                'ticks' => $ticks
            ],
            'height' => 500,
            'interpolateNulls' => true,
            'series' => [
                ['lineDashStyle' => [10, 10], 'color' => 'black'],
                ['lineDashStyle' => [1]],
             ]
        ]);
    }

    private function generateTable($points)
    {
        $result = '<table class="table table-borderd"><thead><tr>';
        foreach ($points[0] as $variable => $value) {
            $column = $this->getColumnName($variable);
            $result .= "<th>$column</th>";
        }
        $result .= '</tr></thead><tbody>';
        foreach ($points as $row) {
            $result .= '<tr>';
            foreach ($row as $variable => $value) {
                $result .= "<td>$value</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    private function getTicks($depth)
    {
        $ticks = [0];
        $counter = 500;
        while ($counter < $depth) {
            $ticks[] = $counter;
            $counter += 500;
        }
        $ticks[] = $counter;
        return $ticks;
    }

    private function getColumnName($column)
    {
        $result = $column;
        if ($column == 'md')
            $result = 'MD (ft)';
        if ($column == 'incDeg')
            $result = 'Inc (&deg)';
        if ($column == 'incRad')
            $result = 'Inc (rad)';
        if ($column == 'azimuthDeg')
            $result = 'Azimuth (&deg)';
        if ($column == 'azimuthRad')
            $result = 'Azimuth (rad)';
        if ($column == 'd1')
            $result = 'D1';
        if ($column == 'd2')
            $result = 'DL (rad)';
        if ($column == 'rf')
            $result = 'RF';
        if ($column == 'tvd')
            $result = 'TVD';
        if ($column == 'north')
            $result = 'North';
        if ($column == 'east')
            $result = 'East';
        if ($column == 'hd')
            $result = 'HD';
        return $result;
    }

    private function getMethodName($method)
    {
        if ($method == 'moc')
            return 'MOC';
        if ($method == 'roc')
            return 'ROC';
        if ($method == 'tan')
            return 'Tangential';
        if ($method == 'avg')
            return 'Angle Averaging';
    }

    private function getSampleData()
    {
        return [
            [200.20, 0.2, 339.50, 200.2, 0.33, -0.12, 0.28],
            [292.10, 0.2, 343.00, 292.1, 0.63, -0.23, 0.54],
            [382.80, 1.2, 355.40, 382.8, 1.73, -0.35, 1.56],
            [474.50, 2.4, 1.10, 474.4, 4.61, -0.39, 4.31],
            [566.20, 4, 1.30, 566.0, 9.72, -0.28, 9.25],
            [659.80, 4.5, 5.60, 659.3, 16.64, 0.15, 16.01],
            [755.50, 5.6, 8.50, 754.7, 25.00, 1.21, 24.32],
            [851.10, 7.4, 10.70, 849.6, 35.66, 3.04, 35.06],
            [946.90, 10.4, 10.90, 944.3, 50.2, 5.8, 49.8],
            [1042.50, 13.5, 14.50, 1037.7, 69.5, 10.2, 69.6],
            [1138.30, 16, 16.90, 1130.5, 92.9, 16.9, 94.0],
            [1234.20, 18.9, 17.90, 1221.9, 120.4, 25.5, 122.7],
            [1330.00, 22.9, 19.30, 1311.4, 152.7, 36.4, 156.8],
            [1426.00, 24.8, 19.40, 1399.2, 189.3, 49.3, 195.7],
            [1521.50, 27.1, 18.80, 1485.1, 228.8, 63.0, 237.3],
        ];
    }

    private function validateInput($input)
    {
        try {
            return count($input);
            $actualInput = !is_null($input[0]['tvd']);

            for ($i = 0; $i < count($input)-1; $i++) {
                $row = $input[$i];
                if (is_null($row['md']) || is_null($row['inc']) || is_null($row['azimuth']))
                    throw new DataException('Your file is not valid because the data is missed one or more rows');

                if (is_nulL($row['tvd']) || is_null($row['north']) || is_null($row['east']) || is_null($row['hd']))
                    $actualInput = false;
            }

            return $actualInput;
        } catch (DataException $exc) {
            throw new Exception($exc->getMessage());
        } catch (Exception $exc) {
            throw new Exception('Your file is not valid because there is something wrong with the format');
        }
    }
}
