<?php
//Project :tovar.uz
// Author: Khankhodjaev A
// Date : 04.11.2020


namespace app\models\layer;


use yii\db\ActiveQuery;


abstract class SortByQuery
{
    protected $sort;
    protected $sort_created;
    protected $sort_viewed;

    public function __construct(array $get)
    {
        $this->sort = (!empty($get['sort-amt']))
            ? (($get['sort-amt'] == "asc")
                ? SORT_ASC
                : SORT_DESC)
            : false;


        $this->sort_created = (!empty($get['sort-created']))
            ? (($get['sort-created'] == "asc")
                ? SORT_ASC
                : SORT_DESC)
            : false;

        $this->sort_viewed = (!empty($get['sort-viewed']))
            ? (($get['sort-viewed'] == "asc")
                ? SORT_ASC
                : SORT_DESC)
            : false;
    }

    abstract public function sorted(ActiveQuery $query):ActiveQuery;

}