<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 03.11.2020


namespace app\models\layer;


interface IMerchantIems
{


    public function existRecord();
    public function getCategories();
    public function getCounted();
    public function getIds(int $offset, int $limit):array ;

}