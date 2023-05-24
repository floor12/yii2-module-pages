<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 06.07.2016
 * Time: 10:41
 */

namespace floor12\pages\components;

use floor12\editmodal\EditModalHelper;
use floor12\pages\assets\IconHelper;
use floor12\pages\assets\PagesAsset;
use floor12\pages\models\Page;
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
    const VIEW = 'sideMenuWidget';
    const VIEW_ADMIN = 'sideMenuWidgetAdmin';

    public $model;
    public $showCurrentPageheader = false;
    public $adminMode;
    public $lang = 'ru';

    private $_pages = [];
    private $viewTemplate = self::VIEW;
    private $_parent = null;

    public function init()
    {
        if ($this->adminMode === null)
            $this->adminMode = Yii::$app->getModule('pages')->adminMode();

        if ($this->adminMode) {
            PagesAsset::register($this->getView());
            $this->viewTemplate = self::VIEW_ADMIN;
        }

        if (!(isset(Yii::$app->getView()->params['currentPage']) && is_object(Yii::$app->getView()->params['currentPage']))) {
            $this->_pages = Page::find()
                ->where(['parent_id' => 0, 'lang' => $this->lang])
                ->orderBy('norder')
                ->all();
            return;
        }

        $this->model = Yii::$app->getView()->params['currentPage'];

        if (isset($this->model) && $this->model->parent_id == 0) {
            $this->_parent = $this->model;
            $this->_pages = Page::find()
                ->where(['parent_id' => 0, 'lang' => $this->lang])
                ->orderBy('norder')
                ->all();
            return;
        }

        if (isset($this->model) && $this->model->parent && $this->model->parent->parent && !$this->model->parent->parent->parent_id) {
            $this->_parent = Page::findOne($this->model->parent->parent_id);
            $this->_pages = Page::find()
                ->where(['parent_id' => $this->model->parent->parent_id, 'lang' => $this->lang])
                ->orderBy('norder')
                ->all();
            return;
        }

        $this->_parent = Page::findOne($this->model->parent_id);
        $this->_pages = Page::find()
            ->where(['parent_id' => $this->model->parent_id, 'lang' => $this->lang])
            ->orderBy('norder')
            ->all();

    }

    function run()
    {
        $nodes = [];
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                if (($this->model && $page->id == $this->model->id))
                    $page->active = true;

                $nodes[] = $this->render($this->viewTemplate, ['model' => $page]);

                if ($this->model && $page->child && (in_array($this->model->id, $page->child_ids) || $page->active)) {
                    $subs = [];
                    foreach ($page->child as $sub) {
                        if (($sub->id == $this->model->id))
                            $sub->active = true;
                        $subs[] = $this->render($this->viewTemplate, ['model' => $sub]);
                    }
                    $nodes[] = Html::tag('ul', implode("\n", $subs), ['class' => 'sideSubMenu']);
                }
            }

        if ($this->adminMode)
            $nodes[] = Html::a(IconHelper::PLUS . ' добавить раздел', null, [
                'onclick' => EditModalHelper::showForm(['page/form'], ['id' => 0, 'parent_id' => $this->model ? $this->model->parent_id : 0]),
                'class' => 'btn btn-default btn-xs page-new btn-block']);

        if ($this->adminMode = Yii::$app->getModule('pages')->adminMode())
            Pjax::begin(['id' => 'sideMenuControl']);

        if ($this->showCurrentPageheader && $this->_parent)
            echo Html::tag('div', $this->_parent->title, ['class' => 'sideMenuTitle']);

        echo Html::tag('ul', implode("\n", $nodes), ['class' => 'sideMenu menu-control ']);

        if ($this->adminMode = Yii::$app->getModule('pages')->adminMode())
            Pjax::end();
    }
}
