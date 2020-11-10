<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace circle;


class CircleAdapter implements ICircle
{
    private CircleAreaLib $square;
    private const PI =3.14;
    public function __construct(CircleAreaLib $square)
    {
        $this->square = $square;
    }

    function circleArea(float $circumference):float
    {
        $radius = $circumference/(2*self::PI);
        return $this->square->getCircleArea(2*$radius);
    }
}