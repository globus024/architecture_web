<?php
/**
 * Domain: tovar.uz
 * User: Azamat Khankhodjaev
 * Date: 25.05.2017 17:44
 */

namespace app\controllers;


use app\models\layer\MerchantProductFactory;
use app\models\layer\MerchantProductListInfo;
use app\models\layer\MerchantProductSort;
use app\models\layer\MerchantServiceFactory;
use app\models\MerchantPhonesStat;
use app\controllers\Controller as ControllerLocal;
use app\models\Contacts;
use app\models\Merchants;
use app\models\layer\MerchantInfo;
use app\models\services\StatCtrl;
use app\models\stat\MerchantStat;
use Yii;
use yii\data\Pagination;
use app\models\seo\SEOServices;
use app\models\layer\MerchantServiceSort;

class CompanyController extends ControllerLocal
{
    const PAGE_SIZE = 40;

    public function actionInfo($id)
    {
        // Redirect with out ru tag
        redirectWOL("/company/info/" . $id);
        $merchant_id = (int)$id;

        $get = Yii::$app->request->get();

        $url_id = "/" . Yii::$app->initparam->current_lang . "/company/info/" . $merchant_id;

        $t_type = !empty($get['t_type'])
            ? $get['t_type']
            : "product";
        $type = !empty($get['t']) ? $get['t'] : false;

        $item_exist = ['product' => false, 'service' => false];


        $merchant = Merchants::find()->where([
            "id" => $merchant_id,
            "approved" => true
        ])->one();

        if (!empty($merchant)) {
            MerchantStat::set($merchant_id);
            // Main information from companies
            $merchant_info_instance = new MerchantInfo($merchant);
            $merchant_info = $merchant_info_instance->getResponse();
            // Seo block tag create(Title, Description)
            SEOServices::createTag(array("company_id" => $merchant_id));

            $categpry_id = !empty($get['category-id'])
                ? (int)$get['category-id']
                : 0;

            $merchant_factory = new MerchantProductFactory($merchant_id, new MerchantProductSort($get), $categpry_id);
            $merchant_products = $merchant_factory->getMerchantItems();

            $merchant_service_factory = new MerchantServiceFactory($merchant_id, new MerchantServiceSort($get), $categpry_id);
            $merchant_services = $merchant_service_factory->getMerchantItems();

            $attr = array('id' => $id, 'type' => 'merchant');
            $sc = new StatCtrl($attr);
            $sc->merchantStat($merchant_id);

            for ($aa = 0; $aa < 1; $aa++) {
                // if company have serv but don't have prods
                $item_exist['product'] = $merchant_products->existRecord();
                $item_exist['service'] = $merchant_services->existRecord();

                if ($t_type == "product") {
                    $pages = new Pagination([
                        'totalCount' => $merchant_products->getCounted(),
                        'pageSize' => self::PAGE_SIZE
                    ]);
                    $pages->pageSizeParam = false;

                    $id_list = $merchant_products->getIds($pages->offset, $pages->limit);
                    $merch_prod_list_info = new MerchantProductListInfo($id_list,'ru');
                    $merch_prod_list_info->getMerchantsDetail();
                    $merch_prod_list_info->getInStocks();
                    $merch_prod_list_info->getViewsList();
                    var_dump($merch_prod_list_info->getProductGiftExist());
                    $categories = $merchant_products->getCategories();
                } else {
                    $pages = new Pagination([
                        'totalCount' => $merchant_services->getCounted(),
                        'pageSize' => self::PAGE_SIZE
                    ]);
                    $pages->pageSizeParam = false;

                    $id_list = $merchant_services->getIds($pages->offset, $pages->limit);
                    $categories = $merchant_services->getCategories();
                }

                if (!isset($get['t_type']) && !$item_exist['product'] && $item_exist['service']) {
                    if ($t_type == 'product') {
                        $aa = -1;   // if company have serv but don't have prods
                    }
                    $t_type = 'service';
                }

            }



            $mdw = $merchant_info_instance->getMerchantWorkDay();
            return $this->render('index', [
                'url_id' => $url_id,
                "t_type" => $t_type,
                "type" => $type,
                "categories" => $categories,
                "id_list" => $id_list,
                "pages" => $pages,
                "merchant_info" => $merchant_info,
                "item_exist" => $item_exist,
                'mdw' => $mdw
            ]);
        } else {
            return $this->render('index', [

            ]);
        }


    }

    public function actionViewPhones($id)
    {

        $contacts = [];
        try {
            $merchant_id = (int)$id;
            $this->layout = "nolayout";

            $contacts = Contacts::findAll(['merchant_id' => $merchant_id]);
            $curent_date = date('Y-m-d');
            $product_phone_stat = MerchantPhonesStat::findOne([
                'merchant_id' => $merchant_id,
                'date_stat' => $curent_date
            ]);
            if (empty($product_phone_stat)) {
                $product_phone_stat = new MerchantPhonesStat();
            }
            $product_phone_stat->merchant_id = $merchant_id;
            $product_phone_stat->date_stat = $curent_date;
            $product_phone_stat->save();

        } catch (\Exception $e) {
            var_dump($e);
        }


        return $this->render('phone', ['phones' => $contacts]);
    }


//    public function beforeSave($insert)
//    {
//        if($this->isNewRecord){
//            $this->view =1;
//            $this->created = date("Y-m-d H:i:s");
//            $this->created_by = Yii::$app->user->getId();
//        }else{
//            $this->view = $this->view + 1;
//            $this->modified = date("Y-m-d H:i:s");
//            $this->modified_by = Yii::$app->user->getId();
//        }
//        return parent::beforeSave($insert);
//    }


}
