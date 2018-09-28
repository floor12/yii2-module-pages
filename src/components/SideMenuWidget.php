<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 06.07.2016
 * Time: 10:41
 */

namespace floor12\pages\components;

use rmrevin\yii\fontawesome\FontAwesome;
use floor12\editmodal\EditModalHelper;
use floor12\pages\Page;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * Class SideMenuWidget
 * @package frontend\components
 *
 * @property Page[] $_pages
 */
class SideMenuWidget extends Widget
{

    public $model;
    public $adminMode = false;

    private $_pages = [];
    private $_parent = null;

    public function init()
    {
        $this->adminMode = Yii::$app->getModule('pages')->adminMode();

        if ($this->model->parent_id == 0) {
            $this->_parent = $this->model;
            $this->_pages = Page::find()
                ->where(['parent_id' => $this->model->id])
                ->orderBy('norder')
                ->all();
        } elseif ($this->model->parent && $this->model->parent->parent && !$this->model->parent->parent->parent_id) {
            $this->_parent = Page::findOne($this->model->parent->parent_id);
            $this->_pages = Page::find()
                ->where(['parent_id' => $this->model->parent->parent_id])
                ->orderBy('norder')
                ->all();
        } else {
            $this->_parent = Page::findOne($this->model->parent_id);
            $this->_pages = Page::find()
                ->where(['parent_id' => $this->model->parent_id])
                ->orderBy('norder')
                ->all();
        }

        parent::init();
    }

    function run()
    {
        $nodes = [];
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                if (($page->id == $this->model->id))
                    $page->active = true;

                $nodes[] = $this->render('sideMenuWidget', ['model' => $page, 'adminMode' => $this->adminMode]);

                if ($page->child && (in_array($this->model->id, $page->child_ids) || $page->active)) {
                    $subs = [];
                    foreach ($page->child as $sub) {
                        if (($sub->id == $this->model->id))
                            $sub->active = true;
                        $subs[] = $this->render('sideMenuWidget', ['model' => $sub, 'adminMode' => $this->adminMode]);
                    }
                    $nodes[] = Html::tag('ul', implode("\n", $subs), ['class' => 'sideSubMenu']);
                }
            }

        if ($this->adminMode)
            $nodes[] = Html::a(FontAwesome::icon('plus') . 'добавить раздел', null, ['onclick' => EditModalHelper::showForm(['page/form'], ['id' => 0, 'parent_id' => $this->model->parent_id]), 'class' => 'btn btn-default btn-xs page-new']);

        Pjax::begin(['id' => 'menuControl']);
        if ($this->_parent)
            echo Html::tag('div', $this->_parent->title, ['class' => 'sideMenuTitle']);
        echo Html::tag('ul', implode("\n", $nodes), ['class' => 'sideMenu menu-control ']);
        Pjax::end();
    }
}