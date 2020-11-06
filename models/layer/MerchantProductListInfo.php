<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 05.11.2020


namespace app\models\layer;


use app\companents\Util;
use app\models\ProductGifts;
use app\models\Products;
use app\models\ProductsStat;
use yii\helpers\Html;

class MerchantProductListInfo
{
    protected $ids;
    protected $product_list;
    protected $name_lang = "name_ru";

    function __construct(array $ids, $lang)
    {
        $this->ids = $ids;
        $this->product_list = $this->getProductList();

        if ($lang == "uz") {
            $this->name_lang = "name_uz";
        }

    }


    protected function getProductList()
    {
        return Products::find()->where(['products.id' => $this->ids]);
    }


    public function getInStocks(): array
    {
        $res = [];
        $instock_pre = clone $this->product_list;
        $in_stocks = $instock_pre->select("products.id,
                       in_stock.$this->name_lang as name_ru,
                       in_stock.color as html_key_words,
                       in_stock.type as html_header")
            ->join('INNER JOIN', 'in_stock', 'in_stock.id=products.in_stock')->all();

        foreach ($in_stocks as $prod_instock) {
            $res[$prod_instock['id']] = [
                "in_stock" => Util::getCapital($prod_instock[$this->name_lang]),
                "color" => $prod_instock['html_key_words'],
                "type" => $prod_instock['html_header'],
            ];
        }
        return $res;

    }

    public function getViewsList(): array
    {
        $res = [];
        if (!empty($this->ids)) {
            $pr_stats = ProductsStat::findAll(['product_id' => $this->ids]);
            if (!empty($pr_stats)) {
                foreach ($pr_stats as $pr_stat) {
                    $res[$pr_stat['product_id']] = $pr_stat['view'];
                }
            }
        }
        return $res;
    }


    public function getMerchantsDetail(): array
    {
        $res = [];
        $product_merchant_pre = clone $this->product_list;
        $products_merchants = $product_merchant_pre->select("products.id,
                       merchants.name_ru as name_ru,
                       products.merchant_id,
                       ownerships.$this->name_lang as html_key_words,
                       cities.$this->name_lang as html_header")
            ->join('INNER JOIN', 'merchants', 'merchants.id=products.merchant_id')
            ->join('INNER JOIN', 'ownerships', 'ownerships.id=merchants.ownership')
            ->join('INNER JOIN', 'merchant_adress', 'merchant_adress.id=merchants.adress_id')
            ->join('INNER JOIN', 'cities', 'cities.id=merchant_adress.city_id')
            ->all();

        foreach ($products_merchants as $product_merchant) {
            $res[$product_merchant['id']] = [
                'merchant_name' => Html::encode($product_merchant['name_ru']),
                'merchant_id' => $product_merchant['merchant_id'],
                'ownerships' => $product_merchant['html_key_words'],
                'city_name' => $product_merchant['html_header'],
            ];
        }
        return $res;

    }

    public function getItemDetails(): array
    {
        $res = [];
        $product_pre = clone $this->product_list;
        $products = $product_pre->select('name_ru, id')->all();

        foreach ($products as $product) {
            $res[$product['id']] = $product['name_ru'];
        }
        return $res;

    }

    public function getProductGiftExist(): array
    {
        $res = [];
        if (!empty($this->ids)) {

            $gifts = ProductGifts::findAll(['product_id' => $this->ids]);
            if (empty($gifts)) {
                return $res;
            }
            foreach ($gifts as $gift) {
                $res[$gift->product_id] = !empty($gift->gift_product_id)
                    ? true
                    : !empty($gift->gift_service_id)
                        ? true : false;
            }
        }
        return $res;
    }




}