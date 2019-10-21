<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 13.04.2018
 * Time: 10:37
 */

namespace floor12\pages\components;


use floor12\pages\assets\PagesAsset;
use floor12\pages\models\Page;
use floor12\pages\models\PageMenuVisibility;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class MobileMenuWidget extends Widget
{
    public $parent_id = 0;
    public $lang = 'ru';
    public $adminMode = false;
    public $model;

    private $_pages = [];

    public function init()
    {
        $this->adminMode = Yii::$app->getModule('pages')->adminMode();

        if ($this->adminMode)
            PagesAsset::register($this->getView());

        $this->_pages = Page::find()
            ->where(['parent_id' => $this->parent_id, 'menu' => PageMenuVisibility::VISIBLE, 'lang' => $this->lang])
            ->orderBy('norder')
            ->with('child')
            ->with('child.child')
            ->all();
        parent::init();
    }

    function run()
    {
        $nodes = [];
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                if ($page->menu != PageMenuVisibility::VISIBLE)
                    continue;
                $currentPage = !empty(Yii::$app->getView()->params['currentPage']) ? Yii::$app->getView()->params['currentPage'] : null;
                $nodes[] = $this->render('mobileMenuWidget', ['model' => $page, 'currentPage' => $currentPage]);
            }

        echo Html::tag('ul', implode("\n", $nodes), ['class' => 'mobileMenu']);

    }
}