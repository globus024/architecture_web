<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace circle;


class SquareAreaLib
{
    public function getSquareArea(float $diagonal)
    {
        $area = ($diagonal**2)/2;

        return $area;
    }
}