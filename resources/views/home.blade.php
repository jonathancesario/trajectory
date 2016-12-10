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

            .content {
                text-align: center;
            }

            .title {
                font-size: 52px;
                margin-bottom: 30px;
            }

            .control-label {
                font-family: 'Raleway', sans-serif;
                font-weight: 400;
            }

            .header {
                font-size: 24px;
                font-weight: 400;
            }
            .right {
                margin-left: 100px;
            }
            .left {
                margin-left: 150px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="title">Trajectory</div>
        </div>
        <div class="container">
            <form class="form-horizontal" method="POST" action="{{ url(' ') }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="header left">Input</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-5 control-label">BUR</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="bur" value={{Input::get('bur')}}>
                                </div>
                                <label class="col-md-2 control-label">deg/100feet</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-5 control-label">KOP</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="kop" value={{Input::get('kop')}}>
                                </div>
                                <label class="col-md-1 control-label">feet</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="header right">Target</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-4 control-label">TVD</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="tvd" value={{Input::get('tvd')}}>
                                </div>
                                <label class="col-md-1 control-label">feet</label>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">North</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="north" value={{Input::get('north')}}>
                                </div>
                                <label class="col-md-1 control-label">feet</label>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">East</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="east" value={{Input::get('east')}}>
                                </div>
                                <label class="col-md-1 control-label">feet</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <center>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-sign-in"></i>Calculate</button>
                    </center>
                </div>
            </form>
        </div>
        <div class="container">
            <div id="trajectory"></div>
            <?php if(isset($lava)) echo $lava->render('LineChart', 'Trajectory', 'trajectory') ?>
        </div>
    </body>
</html>
