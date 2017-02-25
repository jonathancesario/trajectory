<?php namespace App;

use App\Exceptions\DivisionByZeroException;


class ProjectionNumerator
{
    public function calculate($input, $actualInput, $method)
    {
        return $method == 'all'
            ? $this->calculateMethods($input, $actualInput)
            : $this->calculateMethod($input, $actualInput, $method);
    }

    private function calculateMethods($input, $actualInput)
    {
        $moc = $this->moc($input, $actualInput);
        $roc = $this->roc($input, $actualInput);
        $tan = $this->tan($input, $actualInput);
        $avg = $this->avg($input, $actualInput);
        $data = [$moc, $roc, $tan, $avg];
        return $this->getChartAndTableData($input, $actualInput, $data, 6);
    }

    private function calculateMethod($input, $actualInput, $method)
    {
        $data = [call_user_func_array([$this, $method], [$input, $actualInput])];
        return $this->getChartAndTableData($input, $actualInput, $data, 3);
    }

    public function moc($input, $actualInput)
    {
        $data = $this->initiateData(true);
        $counter = 1;
        foreach ($input as $row) {
            $data = $this->setGeneralVariable($data, $counter, $row);
            $data[$counter]['d1'] = (cos($data[$counter]['incRad'] - $data[$counter-1]['incRad'])) -
                ((sin($data[$counter]['incRad']) * sin($data[$counter-1]['incRad'])) *
                (1 - cos($data[$counter]['azimuthRad'] - $data[$counter-1]['azimuthRad'])));
            if ($data[$counter]['d1'] == 0) throw new DivisionByZeroException($counter + 1);
            $data[$counter]['d2'] = atan(sqrt((1 / (pow($data[$counter]['d1'], 2))) - 1));
            if ($data[$counter]['d2'] == 0) throw new DivisionByZeroException($counter + 1);
            $data[$counter]['rf'] = (2 / $data[$counter]['d2']) * tan($data[$counter]['d2'] / 2);
            $data[$counter]['tvd'] = ((($data[$counter]['md'] - $data[$counter-1]['md']) / 2) *
                    (cos($data[$counter-1]['incRad']) + cos($data[$counter]['incRad'])) * $data[$counter]['rf']
                ) + $data[$counter-1]['tvd'];
            $data[$counter]['north'] = ((($data[$counter]['md'] - $data[$counter-1]['md']) / 2) *
                ((sin($data[$counter-1]['incRad']) * cos($data[$counter-1]['azimuthRad'])) +
                    (sin($data[$counter]['incRad']) * cos($data[$counter]['azimuthRad']))
                ) * $data[$counter]['rf']) + $data[$counter-1]['north'];
            $data[$counter]['east'] = (($data[$counter]['md'] - $data[$counter-1]['md']) / 2) *
                (sin($data[$counter-1]['incDeg'] * pi() / 180) *
                    sin($data[$counter-1]['azimuthDeg'] * pi() / 180) +
                    (sin($data[$counter]['incDeg'] * pi() / 180) * sin($data[$counter]['azimuthDeg'] * pi() / 180))
                ) * $data[$counter]['rf'] + $data[$counter-1]['east'];
            $data[$counter]['hd'] = ($data[$counter]['md'] - $data[$counter-1]['md']) / 2
                * (sin($data[$counter-1]['incRad']) + sin($data[$counter]['incRad']))
                * $data[$counter]['rf'] + $data[$counter-1]['hd'];
            $counter++;
        }
        return $data;
    }

    public function roc($input, $actualInput)
    {
        $data = $this->initiateData();
        $counter = 1;
        foreach ($input as $row) {
            $data = $this->setGeneralVariable($data, $counter, $row);
            $tvdDivision = ($data[$counter]['incDeg'] - $data[$counter-1]['incDeg']) * pi() / 180;
            $data[$counter]['tvd'] = $tvdDivision ? (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * (sin($data[$counter]['incDeg'] * pi() / 180) - sin($data[$counter-1]['incDeg'] * pi() / 180))
                    / $tvdDivision
                ) + $data[$counter-1]['tvd'] : $data[$counter-1]['tvd'];
            $northDivision = ($data[$counter]['incDeg'] - $data[$counter-1]['incDeg']) *
                ($data[$counter]['azimuthDeg'] - $data[$counter-1]['azimuthDeg']) * pow(pi() / 180, 2);
            $data[$counter]['north'] = $northDivision ? (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * (cos($data[$counter-1]['incDeg'] * pi() / 180) - cos($data[$counter]['incDeg'] * pi() / 180))
                    * (sin($data[$counter]['azimuthDeg'] * pi() / 180) - sin($data[$counter-1]['azimuthDeg'] * pi() / 180))
                    / $northDivision
                ) + $data[$counter-1]['north'] : $data[$counter-1]['north'];
            $eastDivision = ($data[$counter]['incDeg'] - $data[$counter-1]['incDeg']) *
                ($data[$counter]['azimuthDeg'] - $data[$counter-1]['azimuthDeg']) * pow(pi() / 180, 2);
            $data[$counter]['east'] = $eastDivision ? (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * (cos($data[$counter-1]['incDeg'] * pi() / 180) - cos($data[$counter]['incDeg'] * pi() / 180))
                    * (cos($data[$counter-1]['azimuthDeg'] * pi() / 180) - cos($data[$counter]['azimuthDeg'] * pi() / 180))
                    / $eastDivision
                ) + $data[$counter-1]['east'] : $data[$counter-1]['east'];
            $data[$counter]['hd'] = pow((pow($data[$counter]['north'], 2) + pow($data[$counter]['east'], 2)), 0.5);
            $counter++;
        }
        return $data;
    }

    public function tan($input, $actualInput)
    {
        $data = $this->initiateData();
        $counter = 1;
        foreach ($input as $row) {
            $data = $this->setGeneralVariable($data, $counter, $row);
            $data[$counter]['tvd'] = (($data[$counter]['md'] - $data[$counter-1]['md']) * cos($data[$counter]['incRad']))
                + $data[$counter-1]['tvd'];
            $data[$counter]['north'] = (($data[$counter]['md'] - $data[$counter-1]['md']) * sin($data[$counter]['incRad'])
                    * cos($data[$counter]['azimuthRad'])
                ) + $data[$counter-1]['north'];
            $data[$counter]['east'] = (($data[$counter]['md'] - $data[$counter-1]['md']) * sin($data[$counter]['incRad'])
                    * sin($data[$counter]['azimuthRad'])
                ) + $data[$counter-1]['east'];
            $data[$counter]['hd'] = (($data[$counter]['md'] - $data[$counter-1]['md']) * sin($data[$counter]['incRad']))
                + $data[$counter-1]['hd'];
            $counter++;
        }
        return $data;
    }

    public function avg($input, $actualInput)
    {
        $data = $this->initiateData();
        $counter = 1;
        foreach ($input as $row) {
            $data = $this->setGeneralVariable($data, $counter, $row);
            $data[$counter]['tvd'] = (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * cos(($data[$counter]['incRad'] + $data[$counter-1]['incRad']) / 2)
                ) + $data[$counter-1]['tvd'];
            $data[$counter]['north'] = (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * sin(($data[$counter]['incRad'] + $data[$counter-1]['incRad']) / 2)
                    * cos(($data[$counter]['azimuthRad'] + $data[$counter-1]['azimuthRad']) / 2)
                ) + $data[$counter-1]['north'];
            $data[$counter]['east'] = (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * sin(($data[$counter]['incRad'] + $data[$counter-1]['incRad']) / 2)
                    * sin(($data[$counter]['azimuthRad'] + $data[$counter-1]['azimuthRad']) / 2)
                ) + $data[$counter-1]['east'];
            $data[$counter]['hd'] = (($data[$counter]['md'] - $data[$counter-1]['md'])
                    * sin(($data[$counter]['incRad'] + $data[$counter-1]['incRad']) / 2)
                ) + $data[$counter-1]['hd'];
            $counter++;
        }
        return $data;
    }

    private function initiateData($moc = false)
    {
        $data[0] = ['md' => 0, 'incDeg' => 0, 'incRad' => 0, 'azimuthDeg' => 0, 'azimuthRad' => 0];
        if ($moc) {
            $data[0]['d1'] = 0;
            $data[0]['d2'] = 0;
            $data[0]['rf'] = 0;
        }
        $data[0]['tvd'] = 0;
        $data[0]['north'] = 0;
        $data[0]['east'] = 0;
        $data[0]['hd'] = 0;
        return $data;
    }

    private function setGeneralVariable($data, $counter, $row)
    {
        $data[$counter]['md'] = $row['md'];
        $data[$counter]['incDeg'] = $row['inc'];
        $data[$counter]['incRad'] = $data[$counter]['incDeg'] * pi() / 180;
        $data[$counter]['azimuthDeg'] = $row['azimuth'];
        $data[$counter]['azimuthRad'] = $data[$counter]['azimuthDeg'] * pi() / 180;
        return $data;
    }

    private function getChartAndTableData($input, $actualInput, $data, $length)
    {
        array_unshift($input, [
            'md' => 0, 'inc' => 0, 'azimuth' => 0, 'tvd' => 0, 'north' => 0, 'east' => 0, 'hd' => 0
        ]);
        $input = $actualInput ? $input : [];

        $types = array_merge($data, [$input]);
        list($verticalPoints, $northEastPoints) = $this->getChartData($types, $length);

        $data = $this->getTableData($data, $length);

        return [$verticalPoints, $northEastPoints, $data];
    }

    private function getTableData($data, $length)
    {
        if ($length == 3) {
            $result = array_values($data[0]);
        } else {
            $result = [];
            $moc = $data[0];
            $roc = $data[1];
            $tan = $data[2];
            $avg = $data[3];
            for ($i = 0; $i < count($moc); $i++) {
                $mocRow = $moc[$i];
                $rocRow = $roc[$i];
                $tanRow = $tan[$i];
                $avgRow = $avg[$i];
                $result[] = [
                    $mocRow['md'], $mocRow['incDeg'], $mocRow['azimuthDeg'],
                    $mocRow['tvd'], $mocRow['north'], $mocRow['east'], $mocRow['hd'],
                    $rocRow['tvd'], $rocRow['north'], $rocRow['east'], $rocRow['hd'],
                    $tanRow['tvd'], $tanRow['north'], $tanRow['east'], $tanRow['hd'],
                    $avgRow['tvd'], $avgRow['north'], $avgRow['east'], $avgRow['hd'],
                ];
            }
        }
        return $result;
    }

    private function getChartData($types, $length)
    {
        $verticalPoints = [];
        $northEastPoints = [];
        for ($i = 0; $i < count($types); $i++) {
            $type = $types[$i];
            $verticalPoints = $this->getPoints($verticalPoints, $type, 'hd', 'tvd', $i+1, $length);
            $northEastPoints = $this->getPoints($northEastPoints, $type, 'east', 'north', $i+1, $length);
        }
        return [$verticalPoints, $northEastPoints];
    }

    private function getPoints($points, $data, $var1, $var2, $position, $length)
    {
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {
                $row = $data[$i];
                $temp = [];
                $temp[] = $row[$var1];

                for ($j = 1; $j < $length; $j++) {
                    if ($j == $position) {
                        $temp[] = $row[$var2];
                    } else {
                        $temp[] = null;
                    }
                }
                $points[] = $temp;
            }
        }

        return $points;
    }
}
