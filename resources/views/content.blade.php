@extends('app')

@section('content')
<div class="col-md-3">
    <form class="form-horizontal" method="POST" action="{{ url(' ') }}">
        <input type="hidden" name="totalRow" value={{$totalRow}}>
        <label class="col-md-3 control-label">Row</label>
        <div class="col-md-4">
            <input type="text" class="form-control" name="additionalRow">
        </div>
        <button type="submit" class="btn btn-primary" name="action" value="addRow">Add</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-md-1"><center>Depth (ft)</center></th>
                    <th class="col-md-1"><center>Inc</center></th>
                    <th class="col-md-1"><center>Azimuth</center></th>
                </tr>
            </thead>
            <?php
            for ($i = 0; $i < $totalRow; $i++) {
                $depth = Input::get("depth$i");
                $inc = Input::get("inc$i");
                $azimuth = Input::get("azimuth$i");
                echo
                "<tbody>
                    <tr>
                        <td><input type='text' class='form-control' name='depth$i' value='$depth'></td>
                        <td><input type='text' class='form-control' name='inc$i' value='$inc'></td>
                        <td><input type='text' class='form-control' name='azimuth$i' value='$azimuth'></td>
                    </tr>
                </tbody>";
            }
            ?>
        </table>

        <div class="form-group">
            <center>
                <button type="submit" class="btn btn-primary" name="action" value="calculate">Calculate</button>
            </center>
        </div>
    </form>
</div>
<div class="col-md-9">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#trajectory">Trajectory</a></li>
        <li><a data-toggle="tab" href="#table">Table</a></li>
    </ul>
    <div class="tab-content">
        <div id="trajectory" class="tab-pane fade in active">
            <?php if(isset($lava)) echo $lava->render('LineChart', 'Trajectory', 'trajectory')?>
        </div>
        <div id="table" class="tab-pane fade">
            
        </div>
    </div>
</div>
@endsection
