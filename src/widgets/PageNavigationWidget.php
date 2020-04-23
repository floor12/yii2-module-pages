<?php


namespace floor12\pages\widgets;


use floor12\pages\models\Page;
use yii\base\Widget;
use floor12\pages\components\CurrentPagePath;
use Yii;

class PageNavigationWidget extends Widget
{
    public $activeElementCssClass;
    /** @var string */
    public $ulCssClass;
    /** @var string */
    public $childUlCssClass;
    /** @var string */
    public $lang;
    /** @var int */
    public $parentId = 0;
    /** @var string */
    protected $activePath = [];

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $pathGenerator = new CurrentPagePath();
        $this->activePath = $pathGenerator->getAsArray(Yii::$app->getModule('pages')->currentPageId);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run(): string
    {
        return PageNavigationRecursiveWidget::widget([
            'lang' => $this->lang,
            'parentId' => $this->parentId,
            'activeElementCssClass' => $this->activeElementCssClass,
            'childUlCssClass' => $this->childUlCssClass,
            'ulCssClass' => $this->ulCssClass,
            'activePath' => $this->activePath,
            'ulIsActive' => false,
        ]);
    }
}
