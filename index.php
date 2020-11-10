<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020

use service\BaseDecorator;
use service\Sms;
use service\Facebook;
use service\Slack;
use circle\SquareAdapter;
use circle\SquareAreaLib;
use circle\CircleAdapter;
use circle\CircleAreaLib;

spl_autoload_register(function ($classname){
   require_once ("$classname.php");
});
// task 1
$notificator = new Slack(new Facebook(new Sms(new BaseDecorator())));
$notificator->send('test');

// task2

$square = new SquareAdapter(new SquareAreaLib());
var_dump($square->squareArea(3));

$cirle = new CircleAdapter(new CircleAreaLib());
var_dump($cirle->circleArea(4));