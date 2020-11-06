<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 29.10.2020


namespace app\models\layer;

use app\models\Merchants;
use app\models\Ownerships;
use yii\helpers\Html;
use Yii;

class MerchantInfo
{
    private $merchant;

    public function __construct(Merchants $merchant)
    {
        $this->merchant = $merchant;
    }

    public function getGeo(): string
    {
        $geo = "";
        if (!empty($this->merchant->google_cordinate)) {
            $geo = $this->merchant->google_cordinate;
        }
        return $geo;
    }

    public function getEmail(): string
    {
        $email = "";
        if ($this->merchant->email) {
            $email = $this->merchant->email;
        }
        return $email;
    }

    public function getWebsite(): array
    {
        $web = '';
        if ($this->merchant->website) {
            $web = $this->merchant->website;
            $web = preg_split("/[\s,\s;]+/", Html::encode($web));
        }
        return $web;

    }

    public function getPostIndex(): string
    {
        $post_index = '';
        if ($this->merchant->index) {
            $post_index = $this->merchant->index;
        }
        return $post_index;
    }

    public function getOwnership(): string
    {
        $ownership_name = "";
        if ($this->merchant->ownership) {
            $ownership = Ownerships::findOne([$this->merchant->ownership]);
            $ownership_name = $ownership->getName();
        }
        return $ownership_name;
    }

    public function getLegalName(): string
    {
        $legal_name = '';
        $ownership_name = $this->getOwnership();
        if ($ownership_name) {
            $legal_name = str_replace('"', '', $this->merchant->legal_name);
            $legal_name = $ownership_name . ' "' . $legal_name . '"';
        }
        return $legal_name;
    }

    public function getMerchantName(): string
    {
        return $this->merchant->name_ru;
    }

    public function getContact(): array
    {
        $merchant_contact = new MerchantContacts($this->merchant);
        return $merchant_contact->getContacts();
    }

    public function getUrl(): string
    {
        return getLangUrl("/company/info/" . $this->merchant->id);
    }

    public function getMerchantAdress(): array
    {
        return (new MerchantAdress($this->merchant))->getAdress();
    }

    public function getMerchantDocs(): MerchantDocs
    {
        return new MerchantDocs($this->merchant);
    }

    public function getMerchantWorkDay()
    {
        $sql = "SELECT 
                    merchant_working_day.id 
                FROM merchant_working_day
                INNER JOIN days_of_week ON days_of_week.id=merchant_working_day.work_day_id                
                WHERE merchant_id=" . $this->merchant->id . " ORDER BY
                CASE
                  WHEN days_of_week.day_type='monday' THEN 1
                  WHEN days_of_week.day_type='tuesday' THEN 2
                  WHEN days_of_week.day_type='wednesday' THEN 3
                  WHEN days_of_week.day_type='thursday' THEN 4
                  WHEN days_of_week.day_type='friday' THEN 5
                  WHEN days_of_week.day_type='saturday' THEN 6
                  WHEN days_of_week.day_type='sunday' THEN 7      
                END";
        $mdw = Yii::$app->getDb()->createCommand($sql)->queryAll();
        return $mdw;

    }

    public function getResponse(): array
    {
        $res = [
            "id" => $this->merchant->id,
            "merchant_name" => $this->merchant->getName(),
            'merchant_desc' => $this->merchant->getDesc(),
            "url" => $this->getUrl(),
            "logo" => $this->getMerchantDocs()->getLogo(),
            "banner" => $this->getMerchantDocs()->getBaner(),
            "license" => $this->getMerchantDocs()->getLicense(),
            "adress" => $this->getMerchantAdress(),
            "phones" => $this->getContact(),
            "geo" => $this->getGeo(),
            "web" => $this->getWebsite(),
            "email" => $this->getEmail(),
            "legal_name" => $this->getLegalName(),
            "contact_type_name" => $this->getContact(),
            "index" => $this->getPostIndex(),
        ];
        return $res;
    }


}