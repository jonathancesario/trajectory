<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Trajectory</title>

        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 300;
                height: 100vh;
                margin: 0;
            }

            .title {
                font-size: 52px;
                margin-bottom: 30px;
                margin-left: 25px;
            }

            .control-label {
                font-family: 'Raleway', sans-serif;
                font-weight: 400;
            }

            .header {
                font-size: 26px;
                font-weight: 400;
            }
            .right {
                margin-left: 120px;
            }
            .left {
                margin-left: 175px;
            }

            .width {
                width: 60px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="title">Trajectory</div>
        </div>
        <div class="container">
            <div class="col-md-3">
                <form class="form-horizontal" method="POST" action="{{ url(' ') }}">
                    <div class="header">Input</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-1 col-md-2 control-label">BUR</label>
                            <div class="col-xs-3 col-sm-2 col-md-4">
                                <input type="text" class="form-control width" name="bur" value={{Input::get('bur')}}>
                            </div>
                            <label class="col-xs-1 col-sm-1 col-md-1 control-label">deg/100feet</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-1 col-md-2 control-label">KOP</label>
                            <div class="col-xs-3 col-sm-2 col-md-4">
                                <input type="text" class="form-control width" name="kop" value={{Input::get('kop')}}>
                            </div>
                            <label class="col-xs-1 col-sm-1 col-md-1 control-label">feet</label>
                        </div>
                    </div>
                    <div class="header">Target</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-1 col-md-2 control-label">TVD</label>
                            <div class="col-xs-3 col-sm-2 col-md-4">
                                <input type="text" class="form-control width" name="tvd" value={{Input::get('tvd')}}>
                            </div>
                            <label class="col-xs-1 col-sm-1 col-md-1 control-label">feet</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-1 col-md-2 control-label">North</label>
                            <div class="col-xs-3 col-sm-2 col-md-4">
                                <input type="text" class="form-control width" name="north" value={{Input::get('north')}}>
                            </div>
                            <label class="col-xs-1 col-sm-1 col-md-1 control-label">feet</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-1 col-md-2 control-label">East</label>
                            <div class="col-xs-3 col-sm-2 col-md-4">
                                <input type="text" class="form-control width" name="east" value={{Input::get('east')}}>
                            </div>
                            <label class="col-xs-1 col-sm-1 col-md-1 control-label">feet</label>
                        </div>
                    </div>
                    <div class="panel-body">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-sign-in"></i>Calculate</button>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div id="trajectory"></div>
                <?php if(isset($lava)) echo $lava->render('LineChart', 'Trajectory', 'trajectory') ?>
            </div>
        </div>
    </body>
</html>
