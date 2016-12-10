<?php namespace App\Http\Controllers;

use App;
use Input;
use Request;
use Khill\Lavacharts\Lavacharts;


class TrajectoryController extends Controller
{
    public function calculate()
    {
        if(Request::method() == 'GET')
            return view('home');

        $parameters = Input::all();

        $lava = App::make(Lavacharts::class);

        $trajectory = $lava->DataTable();
        $trajectory
            ->addNumberColumn('Horizontal Departures')
            ->addNumberColumn('True Vertical Depth');

        /* calculate the trajectory */
        $trajectory // dummy data
            ->addRow([0,0])->addRow([0,500])->addRow([0,1000])->addRow([0,1500])
            ->addRow([100,2000])->addRow([250,2500])->addRow([400,3000])->addRow([500,3500])
            ->addRow([650,4000])->addRow([800,4500])->addRow([1000,5000]);

        $lava->LineChart('Trajectory', $trajectory, [
            'hAxis' => ['title' => 'Horizontal Departures'],
            'vAxis' => ['direction' => -1]
        ]);

        return view('home', compact('lava'))->withInput($parameters);
    }
}
