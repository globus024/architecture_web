<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 03.11.2020


namespace app\models\layer;

use app\models\Categories;
use app\models\Products;
use yii\helpers\ArrayHelper;

class MerchantProducts implements IMerchantIems
{
    protected $category_id;
    protected $merchant_id;
    protected $products;
    protected $product_clone;
    protected $sort;


    public function __construct(int $merchant_id, SortByQuery $sort, int $category_id = 0)
    {
        $this->merchant_id = $merchant_id;
        $this->category_id = $category_id;
        $this->sort = $sort;
        $this->products = $this->preBuild();
        $this->product_clone = $this->build();
        $this->sort();

    }


    protected function preBuild()
    {
        $products_pre = Products::find()
            ->where(['products.merchant_id' => $this->merchant_id])
            ->andWhere([
                'products.available' => true,
                'products.active' => true,
                'products.approved' => true
            ]);
        return $products_pre;
    }

    public function getIds(int $offset, int $limit): array
    {

        return ArrayHelper::getColumn($this->product_clone->offset($offset)
            ->limit($limit)
            ->all(), 'id');

    }


    protected function build()
    {

        $product_clone = clone $this->products;
        if (!empty($this->category_id)) {
            $product_clone = $product_clone->andWhere([
                'products.category_id' => $this->category_id]);

        }

        return $product_clone;
    }

    // TODO: Implement existRecord() method.
    public function existRecord()
    {
        if (!empty($this->products)) {
            return $this->products->exists();
        }
        return false;
    }

    // TODO: Implement getCategories() method.
    public function getCategories(string $lang = 'ru'): array
    {
        $ret = [];
        //made_in is a parent_id from categories table
        $product_categories = $this->products->select('products.category_id, 
                categories.name_ru as name_ru, 
                categories.name_uz as name_uz, 
                categories.parent_id as made_in ')
            ->join('INNER JOIN', 'categories',
                'categories.id=products.category_id')
            ->groupBy('products.category_id, categories.name_ru,
             categories.name_uz, categories.parent_id')->all();

        foreach ($product_categories as $product_category) {
            $categ_name = $product_category['name_' . $lang];
            $ret[$product_category->made_in][] = [
                "name" => $categ_name,
                "category_id" => $product_category->category_id
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
       return $this->sort->sorted($this->product_clone);

    }

    // TODO: Implement getCounted() method.
    public function getCounted()
    {
        return !empty($this->product_clone) ? $this->product_clone->count() : 0;
    }
}