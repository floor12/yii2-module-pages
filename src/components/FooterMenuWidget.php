<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 13.04.2018
 * Time: 10:37
 */

namespace floor12\pages\components;


use yii\base\Widget;
use floor12\pages\Page;
use common\components\FontAwesome;
use yii\helpers\Html;
use yii\widgets\Pjax;
use floor12\editmodal\ModalWindow;
use floor12\pages\assets\PagesAsset;
use \Yii;

class FooterMenuWidget extends Widget
{
    public $parent_id;

    private $_pages = [];

    public function init()
    {

        $this->_pages = Page::find()
            ->where(['parent_id' => $this->parent_id, 'menu' => Page::SHOW_IN_MENU])
            ->orderBy('norder')
            ->all();
        parent::init();
    }

    function run()
    {
        $nodes = [];
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                if (strpos('/' . \Yii::$app->request->pathInfo, $page->url) === 0)
                    $page->active = true;

                $subs = [];
                if ($page->child) foreach ($page->child as $sub) {
                    if (strpos('/' . \Yii::$app->request->pathInfo, $sub->url) === 0)
                        $sub->active = true;
                    if ($sub->menu)
                        $subs [] = $this->render('_footerMenuWidget', ['model' => $sub]);
                }
                $nodes[] = $this->render('footerMenuWidget', ['model' => $page, 'subs' => implode("\n", $subs)]);
            }

        return implode("\n", $nodes);
    }
}