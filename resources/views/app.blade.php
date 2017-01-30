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
    <link href="https://fonts.googleapis.com/css?family=Arvo" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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

        .format {
            font-size: 20px;
            margin-bottom: 25px;
        }

        .number {
            font-family: 'Arvo', serif;
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

    <!-- <script type="text/javascript" src="js/chart.js"></script> -->

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var verticalPoints = <?php echo json_encode($verticalPoints); ?>;
            var northEastPoints = <?php echo json_encode($northEastPoints); ?>;
            var method = <?php echo json_encode($method); ?>;

            var data = getData('Vertical Section', method, verticalPoints);
            var verticalData = google.visualization.arrayToDataTable(data);
            var verticalOption = getOption(method, -1, getTicks(verticalPoints));
            var verticalChart = new google.visualization.LineChart(document.getElementById('verticalDiv'));

            var data = getData('West(-)/East(+)', method, northEastPoints);
            var northEastData = google.visualization.arrayToDataTable(data);
            var northEastOption = getOption(method, 1, getTicks(northEastPoints));
            var northEastChart = new google.visualization.LineChart(document.getElementById('northEastDiv'));

            verticalChart.draw(verticalData, verticalOption);
            northEastChart.draw(northEastData, northEastOption);
        }

        function getData(xAxis, method, points) {
            var data = [[xAxis, method, 'Actual']];
            for (var i = 0; i < points.length; i++) {
                data.push(points[i]);
            }
            return data;
        }

        function getOption(method, direction, ticks) {
            return {
                title: method,
                vAxis: {
                    direction: direction,
                    ticks: ticks
                },
                height: 500,
                interpolateNulls: true,
                series: [
                    {lineDashStyle: [10, 10], color: 'black'},
                    {lineDashStyle: [1]},
                ]
            }
        }

        function getTicks(points) {
            var lastPoint = points[points.length-1];
            var depth = Math.max(lastPoint[1], lastPoint[2]);
            ticks = [0];
            counter = 500;
            while (counter < depth) {
                ticks.push(counter);
                counter += 500;
            }
            ticks.push(counter);
            return ticks;
        }
    </script>

</head>
<body>
    <div class="title">Trajectory Evaluation</div>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
