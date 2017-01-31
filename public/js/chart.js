function getData(xAxis, method, points) {
    var data = [[xAxis, {label: method, type: 'number'}, {label: 'Actual', type: 'number'}]];
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
