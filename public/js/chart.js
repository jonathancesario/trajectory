function getData(xAxis, method, points) {
    if (method == 'all') {
        var data = [
            [
                xAxis,
                {label: 'Minimum of Curvature', type: 'number'},
                {label: 'Radius of Curvature', type: 'number'},
                {label: 'Tangential', type: 'number'},
                {label: 'Angle Averaging', type: 'number'},
                {label: 'Actual', type: 'number'}
            ]
        ];
    } else {
        var data = [[xAxis, {label: method, type: 'number'}, {label: 'Actual', type: 'number'}]];
    }

    for (var i = 0; i < points.length; i++) {
        data.push(points[i]);
    }
    
    return data;
}

function getOption(method, direction, ticks) {
    if (method == 'all') {
        var series = [
            {lineDashStyle: [14, 2, 2, 7], color: 'red'},
            {lineDashStyle: [5, 1, 3], color: 'blue'},
            {lineDashStyle: [4, 4], color: 'orange'},
            {lineDashStyle: [2, 2], color: 'green'},
            {lineDashStyle: [1], color: 'black'},
        ];
    } else {
        var series = [
            {lineDashStyle: [10, 10], color: getColor(method)},
            {lineDashStyle: [1], color: 'black'}
        ];
    }

    var option = {
        vAxis: {
            direction: direction,
            ticks: ticks
        },
        height: 500,
        interpolateNulls: true,
        series: series
    };

    if (method != 'all')
        option.title = method;

    return option;
}

function getTicks(points) {
    var lastPoint = points[points.length-1];
    var comparison = lastPoint.slice(0);
    comparison.shift();
    var depth = Math.max.apply(Math, comparison);

    ticks = [0];
    counter = 500;
    while (counter < depth) {
        ticks.push(counter);
        counter += 500;
    }
    ticks.push(counter);

    return ticks;
}

function getColor(method) {
    if (method == 'Minimum of Curvature')
        return 'red';
    if (method == 'Radius of Curvature')
        return 'blue';
    if (method == 'Tangential')
        return 'orange';
    if (method == 'Angle Averaging')
        return 'green';
}
