<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace service;


interface Notificator
{
    public function send(string $messages):void;
}