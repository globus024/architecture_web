<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 05.11.2020


namespace app\models\layer;


use yii\db\ActiveQuery;

class MerchantServiceSort extends SortByQuery
{

    public function sorted(ActiveQuery $query): ActiveQuery
    {
        if ($this->sort) {
            $services = $query->orderBy(['merchant_services.price' => $this->sort]);
        } elseif ($this->sort_created) {
            $services = $query->orderBy(['merchant_services.created' => $this->sort_created]);
        } elseif ($this->sort_viewed) {
            $services = $query->join('LEFT JOIN', 'services_stat',
                'services_stat.service_id=merchant_services.id')
                ->orderBy(['(services_stat.view is not null,0,services_stat.view )' => $this->sort_viewed]);
        } else {
            $services = $query->join('LEFT JOIN', 'top_services',
                'top_services.service_id=merchant_services.id 
                        and top_services.category_id=merchant_services.category_id')
                ->orderBy(['top_services.position' => SORT_ASC]);
        }

        return $services;

    }
}