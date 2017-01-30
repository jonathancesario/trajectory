<?php namespace App;


class ProjectionGenerator
{
    public function generateChart($chart, $points, $name, $direction, $method)
    {
        $method = $this->getMethodName($method);

        /* initialize and setup charts */
        $trajectory = $chart->DataTable();
        $trajectory
            ->addNumberColumn('Horizontal Departures')
            ->addNumberColumn($method)
            ->addNumberColumn('Actual');

        /* add data to charts */
        foreach ($points as $row) {
            $trajectory->addRow([$row[0], $row[1], $row[2]]);
        }

        /* generate the chart */
        $depth = max(end($points)[1], end($points)[2]);
        $ticks = $this->getTicks($depth);
        $chart->LineChart($name, $trajectory, [
            'title' => $method,
            'vAxis' => [
                'direction' => $direction,
                'ticks' => $ticks
            ],
            'height' => 500,
            'interpolateNulls' => true,
            'series' => [
                ['lineDashStyle' => [10, 10], 'color' => 'black'],
                ['lineDashStyle' => [1]],
             ]
        ]);
    }

    public function generateTable($points, $method)
    {
        $method = $this->getMethodName($method);
        $result = "<h3>$method</h3><table class='table table-borderd'><thead><tr>";
        foreach ($points[0] as $variable => $value) {
            $column = $this->getColumnName($variable);
            $result .= "<th class='column'>$column</th>";
        }
        $result .= '</tr></thead><tbody>';
        foreach ($points as $row) {
            $result .= '<tr>';
            foreach ($row as $variable => $value) {
                $value = round(floatval($value), 4);
                $result .= "<td class='number'>$value</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    private function getTicks($depth)
    {
        $ticks = [0];
        $counter = 500;
        while ($counter < $depth) {
            $ticks[] = $counter;
            $counter += 500;
        }
        $ticks[] = $counter;
        return $ticks;
    }

    private function getMethodName($method)
    {
        if ($method == 'moc') return 'Minimum of Curvature';
        if ($method == 'roc') return 'Radius of Curvature';
        if ($method == 'tan') return 'Tangential';
        if ($method == 'avg') return 'Angle Averaging';
    }

    private function getColumnName($column)
    {
        $result = $column;
        if ($column == 'md') $result = 'MD (ft)';
        if ($column == 'incDeg') $result = 'Inc (&deg)';
        if ($column == 'incRad') $result = 'Inc (rad)';
        if ($column == 'azimuthDeg') $result = 'Azimuth (&deg)';
        if ($column == 'azimuthRad') $result = 'Azimuth (rad)';
        if ($column == 'd1') $result = 'D1';
        if ($column == 'd2') $result = 'DL (rad)';
        if ($column == 'rf') $result = 'RF';
        if ($column == 'tvd') $result = 'TVD (ft)';
        if ($column == 'north') $result = 'North (ft)';
        if ($column == 'east') $result = 'East (ft)';
        if ($column == 'hd') $result = 'HD (ft)';
        return $result;
    }
}
