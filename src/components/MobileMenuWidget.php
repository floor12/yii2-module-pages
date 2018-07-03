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
use rmrevin\yii\fontawesome\FontAwesome;
use yii\helpers\Html;
use yii\widgets\Pjax;
use floor12\editmodal\ModalWindow;
use floor12\pages\assets\PagesAsset;
use \Yii;

class MobileMenuWidget extends Widget
{
    public $parent_id;
    public $adminMode = false;
    public $model;

    private $_pages = [];

    public function init()
    {

        PagesAsset::register($this->getView());

        $this->adminMode = Yii::$app->getModule('pages')->adminMode();

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
                if (!$page->menu)
                    continue;

                if ($this->model && $page->id == $this->model->id)
                    $page->active = true;

                $nodes[] = $this->render('mobileMenuWidget', ['model' => $page, 'currentPage' => $this->model]);
            }

//        if ($this->adminMode)
//            $nodes[] = "<li class='new-page'>" . Html::a(FontAwesome::icon('plus'), null, ['onclick' => ModalWindow::showForm(['page/form'], ['id' => 0, 'parent_id' => $this->parent_id])]) . "</li>";

        Pjax::begin(['id' => 'dropdownMenuControl']);
        echo Html::tag('ul', implode("\n", $nodes), ['class' => 'mobileMenu']);
        Pjax::end();
    }
}