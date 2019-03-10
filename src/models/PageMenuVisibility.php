<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 2019-03-10
 * Time: 10:34
 */

namespace floor12\pages\models;


use yii2mod\enum\helpers\BaseEnum;

class PageMenuVisibility extends BaseEnum
{
    const HIDDEN = 0;
    const VISIBLE = 1;

    public static $list = [
        self::VISIBLE => 'Показывать в меню',
        self::HIDDEN => 'Скрыть для меню',
    ];

}