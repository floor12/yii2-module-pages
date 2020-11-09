<?php


namespace floor12\pages\widgets;

use floor12\pages\models\Page;
use yii\base\Widget;
use yii\helpers\Html;

class PageNavigationRecursiveWidget extends Widget
{
    public $activeElementCssClass;
    /** @var string */
    public $ulCssClass;
    /** @var string */
    public $childUlCssClass;
    /** @var string */
    public $lang;
    /** @var int */
    public $parentId;
    /** @var bool */
    public $ulIsActive;
    /** @var string|null */
    public $dropDownIcon;
    /** @var string */
    /** @var array */
    public $activePath = [];
    /*** @var Page[] */
    protected $pages = [];
    /** @var array */
    protected $htmlListElements = [];

    public function init(): void
    {
        $this->loadPages();
        $this->createHtmlElements();

    }

    protected function loadPages(): void
    {
        $this->pages = Page::find()
            ->byLang($this->lang)
            ->active()
            ->visible()
            ->orderBy('norder')
            ->andWhere(['parent_id' => $this->parentId])
            ->all();

    }

    protected function createHtmlElements(): void
    {
        foreach ($this->pages as $page) {

            $htmlLink = Html::a($page->title_menu, $page->getUrl(), [
                'class' => in_array($page->id, $this->activePath) ? $this->activeElementCssClass : NULl
            ]);

            if ($page->childVisible) {
                $htmlLink .= ' ';
                $htmlLink .= $this->dropDownIcon;
                $htmlLink .= self::widget([
                    'lang' => $this->lang,
                    'parentId' => $page->id,
                    'activePath' => $this->activePath,
                    'ulCssClass' => $this->childUlCssClass,
                    'childUlCssClass' => $this->childUlCssClass,
                    'activeElementCssClass' => $this->activeElementCssClass,
                    'ulIsActive' => in_array($page->id, $this->activePath)
                ]);
            }

            $cssClass = $page->childVisible ? $this->childUlCssClass : '';

            if (in_array($page->id, $this->activePath)) {
                $cssClass .= ' ';
                $cssClass .= $this->activeElementCssClass;
            }

            $this->htmlListElements[] = Html::tag('li', $htmlLink, [
                'class' => trim($cssClass)
            ]);
        }
    }

    public function run(): string
    {
        $finalUlCssClass = $this->ulCssClass;
        if ($this->ulIsActive)
            $finalUlCssClass .= ' ' . $this->activeElementCssClass;
        return Html::tag('ul', implode($this->htmlListElements), [
            'class' => $finalUlCssClass
        ]);
    }

}
