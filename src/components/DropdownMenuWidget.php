<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 13.04.2018
 * Time: 10:37
 */

namespace floor12\pages\components;


use floor12\editmodal\EditModalHelper;
use yii\base\Widget;
use floor12\pages\Page;
use common\components\FontAwesome;
use yii\helpers\Html;
use yii\widgets\Pjax;
use floor12\editmodal\ModalWindow;
use floor12\pages\assets\PagesAsset;
use \Yii;

class DropdownMenuWidget extends Widget
{
    public $parent_id;
    public $adminMode = false;

    private $_pages = [];

    public function init()
    {

        PagesAsset::register($this->getView());

        $this->adminMode = Yii::$app->getModule('pages')->adminMode();

        $this->_pages = Page::find()
            ->where(['parent_id' => $this->parent_id])
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

                $nodes[] = $this->render('dropdownMenuWidget', ['model' => $page, 'adminMode' => $this->adminMode]);
            }

        if ($this->adminMode)
            $nodes[] = "<li class='new-page'>" . Html::a(FontAwesome::icon('plus'), null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], ['id' => 0, 'parent_id' => $this->parent_id])]) . "</li>";

        Pjax::begin(['id' => 'dropdownMenuControl']);
        echo Html::tag('ul', implode("\n", $nodes), ['class' => 'dropDownMenu dropDownMenu-control']);
        Pjax::end();
    }
}