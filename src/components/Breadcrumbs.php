<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 16.04.2017
 * Time: 10:57
 */

namespace floor12\pages\components;

use yii\base\Widget;
use \Yii;

class Breadcrumbs extends Widget
{
    public $indexPage;
    public $items;

    public function init()
    {
        if (!$this->indexPage)
            $this->indexPage = Yii::$app->name;
        parent::init();
    }

    public function run()
    {
        $counter = 1;
        $breadcrumbs = "<ol itemscope itemtype='http://schema.org/BreadcrumbList' class='breadcrumbs hidden-xs' id='breadcrumbs'>";
        $breadcrumbs .= "<li itemprop=\"itemListElement\" itemscope  itemtype=\"http://schema.org/ListItem\"><a data-pjax='0' itemprop=\"item\" href='/'><span itemprop=\"name\">{$this->indexPage}</span></a><meta itemprop=\"position\" content=\"{$counter}\" /></li>";

        if ($this->items) foreach ($this->items as $key => $val) {
            $counter++;
            if (!is_numeric($key)) {
                $breadcrumbs .= "<li itemprop=\"itemListElement\" itemscope  itemtype=\"http://schema.org/ListItem\"><a  data-pjax='0' itemprop=\"item\" href='{$key}'><span itemprop=\"name\">{$val}</span></a><meta itemprop=\"position\" content=\"{$counter}\" /></li>";
            } else
                $breadcrumbs .= "<li  itemprop=\"itemListElement\" itemscope  itemtype=\"http://schema.org/ListItem\"><span itemprop=\"name\">{$val}</span>  <meta itemprop=\"position\" content=\"{$counter}\" /></li>";
        }

        $breadcrumbs .= "</ol>";

        return $breadcrumbs;
    }

}