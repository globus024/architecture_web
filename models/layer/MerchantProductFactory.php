<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 04.11.2020


namespace app\models\layer;


class MerchantProductFactory extends MerchantItemsFactory
{

    protected function createMerchantItems(int $merchant_id, SortByQuery $sort, int $category_id = 0): IMerchantIems
    {
        return new MerchantProducts($merchant_id, $sort, $category_id);
    }
}