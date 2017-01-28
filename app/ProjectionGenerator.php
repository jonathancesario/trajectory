<?php namespace App;

use Exception;


class ProjectionGenerator
{
    public function moc($input, $actualInput)
    {
        $data = $this->initiateData(true);

        $counter = 1;
        foreach ($input as $row) {
            $data = $this->setGeneralVariable($data, $counter, $row);
            $data[$counter]['d1'] = (cos($data[$counter]['incRad'] - $data[$counter-1]['incRad'])) -
                ((sin($data[$counter]['incRad']) * sin($data[$counter-1]['incRad'])) *
                (1 - cos($data[$counter]['azimuthRad'] - $data[$counter-1]['azimuthRad'])));
            $data[$counter]['d2'] = atan(sqrt((1 / (pow($data[$counter]['d1'], 2))) - 1));
            $data[$counter]['rf'] = (2 / $data[$counter]['d2']) * tan($data[$counter]['d2'] / 2);
            $data[$counter]['tvd'] = ((($data[$counter]['md'] - $data[$counter-1]['md']) / 2) *
                (cos($data[$counter-1]['incRad']) + cos($data[$counter]['incRad'])) * $data[$counter]['rf'])
                + $data[$counter-1]['tvd'];
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

        return $this->getChartAndTableData($input, $actualInput, $data);
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

        return $this->getChartAndTableData($input, $actualInput, $data);
    }

    private function initiateData($moc = false)
    {
        $data[0] = [
            'md' => 0, 'incDeg' => 0, 'incRad' => 0, 'azimuthDeg' => 0, 'azimuthRad' => 0,
            'tvd' => 0, 'north' => 0, 'east' => 0, 'hd' => 0
        ];
        if ($moc) {
            $data[0]['d1'] = 0;
            $data[0]['d2'] = 0;
            $data[0]['rf'] = 0;
        }
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

    private function getChartAndTableData($input, $actualInput, $data)
    {
        array_unshift($input, [
            'md' => 0, 'inc' => 0, 'azimuth' => 0, 'tvd' => 0, 'north' => 0, 'east' => 0, 'hd' => 0
        ]);
        $input = $actualInput ? $input : [];

        $verticalPoints = $this->getPoints($data, $input, 'hd', 'tvd');
        $northEastPoints = $this->getPoints($data, $input, 'east', 'north');
        return [$verticalPoints, $northEastPoints, $data];
    }

    private function getPoints($data, $input, $var1, $var2)
    {
        $points = [];

        for ($i = 0; $i < count($data)-1; $i++) {
            $row = $data[$i];
            $points[] = [$row[$var1], $row[$var2], null];
        }

        for ($i = 0; $i < count($input)-1; $i++) {
            $row = $input[$i];
            $points[] = [$row[$var1], null, $row[$var2]];
        }

        return $points;
    }
}
