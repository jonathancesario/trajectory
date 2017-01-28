@extends('app')

@section('content')
<center class="container">
	<div class="row"><div class="col-md-6 col-md-offset-3">
		<?php if (isset($alert)) echo "<div class='alert alert-warning'>$alert</div>";?>
	</div></div>
	<form class="form-horizontal" method="POST" action="{{ url(' ') }}" enctype="multipart/form-data">
        <div class="form-group">
            <button class="btn btn-success" name="action" value="export">Download sample file</button>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#format">Format</button>
			<div class="modal fade" id="format" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<p>MD(ft), Inc(&deg), Azimuth(&deg) have to be filled.</p>
						<p>TVD, North, East, and HD are optional. We will only generate it if the data is completed.</p>
						<p>Don't start the input with 0, we will add it for you.</p>
						<p>If you want to reduce the row(s), please block that particular row(s), right click on your mouse, and choose delete.
						Otherwise, we will still treat it as row(s) and your file would be invalid.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
        </div>
        <div class="form-group">
            <select class="selectpicker" name="method">
                <option value="moc">MOC</option>
                <option value="roc">ROC</option>
                <option value="tan">Tangential</option>
                <option value="avg">Angle Averaging</option>
            </select>
        </div>
        <div class="form-group">
            <div class="col-md-5"></div><div class="col-md-1"><input type="file" name="data"></div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" name="action" value="import">Calculate</button>
        </div>
	</form>
</center>
<div class="container">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#verticalProjection">Projection - Vertical</a></li>
        <li><a data-toggle="tab" href="#northEastProjection">Projection - North East</a></li>
        <li><a data-toggle="tab" href="#table">Table</a></li>
    </ul>
    <div class="tab-content">
        <?php
            if (isset($chart)) {
                echo $chart->render('LineChart', 'Vertical', 'verticalProjection');
                echo $chart->render('LineChart', 'NorthEast', 'northEastProjection');
            }
        ?>
        <div id="verticalProjection" class="tab-pane fade in active"></div>
		<div id="northEastProjection" class="tab-pane fade"></div>
        <div id="table" class="tab-pane fade">
			<?php if (isset($table)) echo $table; ?>
		</div>
    </div>
</div>
@endsection
