<?php


namespace floor12\pages\components;


use yii\base\Widget;

class MapYandexWidget extends Widget
{
    public $key = 'constructor%3ALZSI9r4tAxh00zlXPFA5z2YG3jjH0r0j';

    public function run()
    {
        return '<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=' . $this->key . '&amp;width=100%25&amp;height=480&amp;lang=ru_RU&amp;scroll=true"></script>';
    }
}