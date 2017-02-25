<?php namespace App\Http\Controllers;

use App\Exceptions\DataException;
use App\Exceptions\DivisionByZeroException;
use App\ProjectionNumerator;
use App\TableGenerator;
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
        $this->generator = App::make(TableGenerator::class);
        $this->numerator = App::make(ProjectionNumerator::class);
    }

    public function show()
    {
        $data = ['verticalPoints' => [], 'northEastPoints' => [], 'method' => ''];
        $action = Input::get('action');
        $method = Input::get('method');

        if (Request::method() == 'POST') {
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
                        list($verticalPoints, $northEastPoints, $calculation) = $this->numerator->calculate($input, $actualInput, $method);
                    } catch (DivisionByZeroException $exc) {
                        $row = $exc->getMessage();
                        return view('content')->with(array_merge([
                            'alert' => "Your file is not valid because of division by zero at line $row."
                        ], $data));
                    } catch (Exception $exc) {
                        return view('content')->with(array_merge(['alert' => $exc->getMessage()], $data));
                    }

                    $method = $this->generator->getMethodName($method);
                    $table = $this->generator->generateTable($calculation, $method);

                    $data = [
                        'verticalPoints' => $verticalPoints, 'northEastPoints' => $northEastPoints,
                        'method' => $method, 'table' => $table
                    ];
        		}
            }
        }
        return view('content')->with($data);
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
            [1617.40, 30.7, 18.50, 1569.0, 272.7, 77.8, 283.6],
            [1713.20, 34.6, 17.50, 1649.7, 321.9, 93.7, 335.2],
            [1808.80, 38, 17.50, 1726.7, 375.8, 110.7, 391.8],
            [1904.50, 36.6, 17.20, 1802.8, 431.2, 128.0, 449.8],
            [2000.20, 37.2, 17.10, 1879.4, 486.1, 145.0, 507.2],
            [2096.10, 37.8, 15.70, 1955.5, 542.1, 161.4, 565.6],
            [2191.70, 35.2, 14.70, 2032.2, 596.9, 176.3, 622.4],
            [2247.90, 34, 14.30, 2078.5, 627.8, 184.3, 654.3],
            [2287.50, 33.53, 12.00, 2111.5, 649.2, 189.3, 676.3],
            [2295.00, 33.65, 12.06, 2117.7, 653.3, 190.2, 680.4],
            [2383.40, 35.1, 12.80, 2190.7, 702.0, 201.0, 730.2],
            [2479.30, 36.1, 15.60, 2268.7, 756.3, 214.7, 786.0],
            [2575.10, 37.7, 18.40, 2345.2, 811.2, 231.5, 843.5],
            [2670.80, 37.8, 17.70, 2420.9, 866.9, 249.7, 902.1],
            [2766.90, 38.4, 18.10, 2496.5, 923.4, 267.9, 961.3],
            [2862.60, 38.1, 16.90, 2571.7, 979.8, 285.7, 1020.5]
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
