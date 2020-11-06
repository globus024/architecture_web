<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 04.11.2020


namespace app\models\layer;


abstract class MerchantItemsFactory
{

    private $merchant_items;

    public function __construct($merchant_id, SortByQuery $sort, $category_id = 0)
    {
        $this->merchant_items = $this->createMerchantItems($merchant_id, $sort, $category_id);
    }

    public function getMerchantItems()
    {
        return $this->merchant_items;
    }

    abstract protected function createMerchantItems(int $merchant_id, SortByQuery $sort,  int $category_id = 0): IMerchantIems;
}