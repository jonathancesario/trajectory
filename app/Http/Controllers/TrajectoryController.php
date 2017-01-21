<?php namespace App\Http\Controllers;

use App;
use Input;
use Request;
use Khill\Lavacharts\Lavacharts;


class TrajectoryController extends Controller
{
    public function show()
    {
        if (Request::method() == 'GET')
            return view('content')->with(['totalRow' => 10]);

        $totalRow = Input::get('totalRow');
        $action = Input::get('action');
        if ($action == 'addRow') {
            return view('content')->with(['totalRow' => $totalRow + Input::get('additionalRow')]);
        } else if ($action == 'calculate') {
            /* initialize and setup charts */
            $lava = App::make(Lavacharts::class);
            $trajectory = $lava->DataTable();
            $trajectory
                ->addNumberColumn('Horizontal Departures')
                ->addNumberColumn('True Vertical Depth');

            /* calculate input and add data to charts */
            $inputs = Input::all();
            $data = $this->calculate($inputs);
            foreach ($data as $row) {
                $trajectory->addRow($row);
            }

            /* generate the chart */
            $lava->LineChart('Trajectory', $trajectory, [
                'hAxis' => ['title' => 'Horizontal Departures'],
                'vAxis' => ['direction' => -1],
                'chartArea' => ['width' => '75%']
            ]);

            return view('content', compact('lava'))->withInput($inputs)->with(['totalRow' => $totalRow]);
        }
    }

    private function calculate($inputs)
    {
        return [
            [0,0], [0,500], [0,1000], [0,1500], [100,2000], [250,2500],
            [400,3000], [500,3500], [650,4000], [800,4500], [1000,5000]
        ];
    }
}
