<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 13.04.2018
 * Time: 10:37
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

class DropdownMenuWidget extends Widget
{
    const VIEW = 'dropdownMenuWidget';
    const VIEW_ADMIN = 'dropdownMenuWidgetAdmin';

    public $parent_id = 0;
    public $adminMode;
    public $onlyRoot = false;
    public $lang = 'ru';

    private $pages = [];
    private $nodes = [];
    private $viewTemplate = self::VIEW;

    public function init()
    {
        if ($this->adminMode === null)
            $this->adminMode = Yii::$app->getModule('pages')->adminMode();

        if ($this->adminMode) {
            PagesAsset::register($this->getView());
            $this->viewTemplate = self::VIEW_ADMIN;
        }

        $this->pages = Page::find()
            ->where(['parent_id' => $this->parent_id, 'lang' => $this->lang])
            ->orderBy('norder')
            ->with('child')
            ->with('child.child')
            ->all();
        parent::init();
    }

    function run()
    {
        if ($this->pages)
            foreach ($this->pages as $page) {

                if ($page->path == '/' && empty(\Yii::$app->request->pathInfo))
                    $page->active = true;

                if (strpos('/' . \Yii::$app->request->pathInfo, '/' . $page->path) === 0)
                    $page->active = true;

                $this->nodes[] = $this->render($this->viewTemplate, [
                    'model' => $page,
                    'adminMode' => $this->adminMode,
                    'onlyRoot' => $this->onlyRoot]);
            }

        if ($this->adminMode)
            $this->nodes[] = "<li class='new-page'>" . Html::a(IconHelper::PLUS, null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], ['id' => 0, 'parent_id' => $this->parent_id])]) . "</li>";

        if ($this->adminMode)
            Pjax::begin(['id' => 'dropdownMenuControl']);

        echo Html::tag('ul', implode(PHP_EOL, $this->nodes), ['class' => 'dropDownMenu dropDownMenu-control']);

        if ($this->adminMode)
            Pjax::end();
    }
}
