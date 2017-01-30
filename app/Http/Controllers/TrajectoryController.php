<?php namespace App\Http\Controllers;

use App\Exceptions\DataException;
use App\Exceptions\DivisionByZeroException;
use App\ProjectionGenerator;
use App\ProjectionNumerator;
use App;
use Excel;
use Exception;
use Input;
use Request;


class TrajectoryController extends Controller
{
    private $generator;
    private $numerator;

    public function __construct()
    {
        $this->generator = App::make(ProjectionGenerator::class);
        $this->numerator = App::make(ProjectionNumerator::class);
    }

    public function show()
    {
        $data = [
            'verticalPoints' => [], 'northEastPoints' => [], 'method' => ''
        ];
        if (Request::method() == 'GET')
            return view('content')->with($data);

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
                $input = Excel::load($path, function($reader) {})->get()->toArray();

                try {
                    $actualInput = $this->validateInput($input);
                    list($verticalPoints, $northEastPoints, $input) = call_user_func_array(
                        [$this->numerator, $method], [$input, $actualInput]
                    );
                } catch (DivisionByZeroException $exc) {
                    $row = $exc->getMessage();
                    return view('content')->with(array_merge([
                        'alert' => "Your file is not valid because of division by zero at line $row."
                    ], $data));
                } catch (Exception $exc) {
                    return view('content')->with(array_merge(['alert' => $exc->getMessage()], $data));
                }

                $table = $this->generator->generateTable($input, $method);
                $method = $this->getMethodName($method);

                // list($verticalPoints, $method) = $this->generator->generateChart($verticalPoints, 'Vertical', -1, $method);
                // $this->generator->generateChart($northEastPoints, 'NorthEast', 1, $method);

                return view('content')->with([
                    'verticalPoints' => $verticalPoints, 'northEastPoints' => $northEastPoints,
                    'method' => $method, 'table' => $table]);
    		}
            return view('content')->with($data);
        }
    }

    private function getMethodName($method)
    {
        if ($method == 'moc') return 'Minimum of Curvature';
        if ($method == 'roc') return 'Radius of Curvature';
        if ($method == 'tan') return 'Tangential';
        if ($method == 'avg') return 'Angle Averaging';
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
