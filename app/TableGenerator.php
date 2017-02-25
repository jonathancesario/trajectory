<?php namespace App;


class TableGenerator
{
    public function generateTable($points, $method)
    {
        if ($method == 'all') {
            $result = "
                <div class='allTable'><table class='table table-bordered'><thead><tr>
                <th class='column' rowspan='2'>MD (ft)</th>
                <th class='column' rowspan='2'>Inc (&deg)</th>
                <th class='column' rowspan='2'>Azimuth (&deg)</th>
                <th class='column' colspan='4'>Minimum of Curvature</th>
                <th class='column' colspan='4'>Radius of Curvature</th>
                <th class='column' colspan='4'>Tangential</th>
                <th class='column' colspan='4'>Angle Averaging</th></tr><tr>
            ";
            $calculations = ['TVD (ft)', 'North (ft)', 'East (ft)', 'HD (ft)'];
            for ($i = 0; $i < 4; $i++) {
                foreach ($calculations as $column) {
                    $result .= "<th class='column'>$column</th>";
                }
            }
        } else {
            $result = "<h3>$method</h3><table class='table table-bordered'><thead><tr>";
            foreach ($points[0] as $variable => $value) {
                $column = $this->getColumnName($variable);
                $result .= "<th class='column'>$column</th>";
            }
        }

        $result .= '</tr></thead><tbody>';
        foreach ($points as $row) {
            $result .= '<tr>';
            foreach ($row as $value) {
                $number = round(floatval($value), 4);
                $number = sprintf("%.4f", $number);
                $result .= "<td class='number'>$number</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';

        if ($method == 'all')
            $result .= '</div>';

        return $result;
    }

    public function getMethodName($method)
    {
        if ($method == 'moc') return 'Minimum of Curvature';
        if ($method == 'roc') return 'Radius of Curvature';
        if ($method == 'tan') return 'Tangential';
        if ($method == 'avg') return 'Angle Averaging';
        return $method;
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
