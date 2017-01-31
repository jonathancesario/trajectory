<?php namespace App;


class TableGenerator
{
    public function generateTable($points, $method)
    {
        $result = "<h3>$method</h3><table class='table table-borderd'><thead><tr>";
        foreach ($points[0] as $variable => $value) {
            $column = $this->getColumnName($variable);
            $result .= "<th class='column'>$column</th>";
        }
        $result .= '</tr></thead><tbody>';
        foreach ($points as $row) {
            $result .= '<tr>';
            foreach ($row as $variable => $value) {
                $number = round(floatval($value), 4);
                $number = sprintf("%.4f", $number);
                $result .= "<td class='number'>$number</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    public function getMethodName($method)
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
