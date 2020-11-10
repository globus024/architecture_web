<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 10.11.2020


namespace service;




class Sms implements Notificator
{
    private Notificator $notificator;
    public function __construct($notificator)
    {
        $this->notificator = $notificator;
    }

    public function send(string $messages): void
    {
       echo "Send to SMS messages: $messages".PHP_EOL;
       $this->notificator->send($messages);
    }
}