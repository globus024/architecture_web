<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace circle;


class SquareAdapter implements ISquare
{
    private SquareAreaLib $square;

    public function __construct(SquareAreaLib $square)
    {
        $this->square = $square;
    }

    function squareArea(float $sideSquare): float
    {
        $diagonal = sqrt(2) * $sideSquare;
        return $this->square->getSquareArea($diagonal);
    }
}