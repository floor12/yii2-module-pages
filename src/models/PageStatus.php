<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 2019-03-10
 * Time: 10:34
 */

namespace floor12\pages\models;


use yii2mod\enum\helpers\BaseEnum;

class PageStatus extends BaseEnum
{
    const ACTIVE = 0;
    const DISABLED = 1;

    public static $list = [
        self::STATUS_ACTIVE => 'Активна',
        self::STATUS_DISABLED => 'Выключена',
    ];

}