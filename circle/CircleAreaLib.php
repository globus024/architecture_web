<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace circle;


class CircleAreaLib
{
    public function getCircleArea(float $diagonal)
    {
        $area = (M_PI * $diagonal ** 2) / 4;

        return $area;
    }
}