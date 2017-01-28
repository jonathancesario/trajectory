<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Trajectory</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

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
                text-align: center;
                font-size: 52px;
                margin-bottom: 30px;
            }

            .control-label {
                font-family: 'Raleway', sans-serif;
                font-weight: 400;
            }

            .selectpicker {
                left: 50%;
                right: auto;
                transform: translate(-50%, 0);
            }

            .modal-content {
                text-align: left;
            }

            .column {
                white-space: nowrap;
            }

            .tab-content > .tab-pane,
            .pill-content > .pill-pane {
                display: block;
                height: 0;
                overflow-y: hidden;
            }

            .tab-content > .active,
            .pill-content > .active {
                height: auto;
            }
        </style>
    </head>
    <body>
        <div class="title">Trajectory</div>
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
