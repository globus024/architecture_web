<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 05.11.2020


namespace app\models\layer;


use app\models\Categories;
use app\models\MerchantServices;
use yii\helpers\ArrayHelper;
class MerchantSerices implements IMerchantIems
{
    protected $category_id;
    protected $merchant_id;
    protected $mservices;
    protected $mservices_clone;
    protected $sort;

    public function __construct(int $merchant_id, SortByQuery $sort, int $category_id = 0)
    {
        $this->merchant_id = $merchant_id;
        $this->category_id = $category_id;
        $this->sort = $sort;
        $this->mservices = $this->preBuild();
        $this->mservices_clone = $this->build();
        $this->sort();

    }


    protected function preBuild()
    {
        $services = MerchantServices::find()
            ->where([
                'merchant_services.merchant_id' => $this->merchant_id
            ])
            ->andWhere([
                "merchant_services.available" => true,
                "merchant_services.active" => true,
                "merchant_services.approved" => true,
            ]);
        return $services;
    }

    public function getIds(int $offset, int $limit): array
    {

        return ArrayHelper::getColumn($this->mservices_clone->offset($offset)
            ->limit($limit)
            ->all(), 'id');

    }


    protected function build()
    {

        $services_clone = clone $this->mservices;
        if (!empty($this->category_id)) {
            $services_clone = $services_clone->andWhere([
                'merchant_services.category_id' => $this->category_id]);

        }

        return $services_clone;
    }


    public function existRecord()
    {
        if (!empty($this->mservices)) {
            return $this->mservices->exists();
        }
        return false;
    }


    public function getCategories(string $lang = 'ru'): array
    {
        $ret = [];
        //made_in is a parent_id from categories table
        $mservices_categories = $this->mservices->select('merchant_services.category_id, 
                categories.name_ru as name_ru, 
                categories.name_uz as name_uz, 
                categories.parent_id as fill_percent ')
            ->join('INNER JOIN', 'categories',
                'categories.id=merchant_services.category_id')
            ->groupBy('merchant_services.category_id, categories.name_ru,
             categories.name_uz, categories.parent_id')->all();

        foreach ($mservices_categories as $mservices_category) {
            $categ_name = $mservices_category['name_' . $lang];
            $ret[$mservices_category->fill_percent][] = [
                "name" => $categ_name,
                "category_id" => $mservices_category->category_id
            ];
        }
        $result = [];
        $parent_ids = array_keys($ret);
        $parent_categories = Categories::findAll(['id' => $parent_ids]);
        foreach ($parent_categories as $parent_category) {
            $result[$parent_category->getName()] = $ret[$parent_category->id];
        }
        return $result;

    }


    // TODO: Implement sort() method.
    protected function sort()
    {
        return $this->sort->sorted($this->mservices_clone);

    }


    public function getCounted()
    {
        // TODO: Implement getCounted() method.
    }
}