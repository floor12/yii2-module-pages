<?php


namespace floor12\pages\widgets;


use floor12\pages\components\CurrentPagePath;
use floor12\pages\models\Page;
use Yii;
use yii\base\Widget;
use yii\caching\TagDependency;

class PageNavigationWidget extends Widget
{
    public $activeElementCssClass = 'active';
    /** @var string */
    public $ulCssClass = 'menu';
    /** @var string */
    public $childUlCssClass = 'dropdown';
    /** @var string */
    public $lang;
    /** @var int */
    public $parentId = 0;
    /** @var string|null */
    public $dropDownIcon = null;
    /** @var string */
    protected $activePath = [];
    /** @var integer */
    protected $currentPageId;
    /** @var TagDependency */
    protected $tagDependency;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        if (empty($this->lang))
            $this->lang = Yii::$app->language;
        $this->currentPageId = Yii::$app->getModule('pages')->currentPageId;
        $this->tagDependency = new TagDependency(['tags' => [Page::CACHE_TAG_NAME]]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run(): string
    {
        return Yii::$app->cache->getOrSet($this->generateCacheKey(), function () {
            $pathGenerator = new CurrentPagePath();
            $this->activePath = $pathGenerator->getAsArray($this->currentPageId);
            return PageNavigationRecursiveWidget::widget([
                'lang' => $this->lang,
                'parentId' => $this->parentId,
                'activeElementCssClass' => $this->activeElementCssClass,
                'childUlCssClass' => $this->childUlCssClass,
                'ulCssClass' => $this->ulCssClass,
                'activePath' => $this->activePath,
                'dropDownIcon' => $this->dropDownIcon,
                'ulIsActive' => false,
            ]);
        }, 60 * 60, $this->tagDependency);

    }

    protected function generateCacheKey(): array
    {
        return [
            $this->lang,
            $this->currentPageId,
            $this->parentId,
            $this->activeElementCssClass,
            $this->dropDownIcon,
            $this->ulCssClass,
            $this->childUlCssClass,
        ];
    }
}
