<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 04.11.2020


namespace app\models\layer;


use yii\db\ActiveQuery;


class MerchantProductSort extends SortByQuery
{


    public function sorted(ActiveQuery $query): ActiveQuery
    {

        if ($this->sort) {
            $products = $query->join('LEFT JOIN', 'prices',
                'prices.product_id=products.id')
                ->orderBy(['(prices.price is not null,0,prices.price)' => $this->sort]);
        } elseif ($this->sort_created) {
            $products = $query->orderBy(['products.created' => $this->sort_created]);
        } elseif ($this->sort_viewed) {
            $products = $query->join('LEFT JOIN', 'products_stat',
                'products_stat.product_id=products.id')
                ->orderBy(['(products_stat.view is not null,0,products_stat.view )' => $this->sort_viewed]);
        } else {
            $products = $query->join('LEFT JOIN', 'top_products',
                'top_products.product_id=products.id and top_products.category_id=products.category_id')
                ->orderBy(['top_products.position' => SORT_ASC]);
        }

        return $products;

    }
}