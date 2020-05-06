<?php


namespace floor12\pages\widgets;

use floor12\pages\models\Page;
use Yii;
use yii\base\Widget;
use yii\caching\TagDependency;
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
    /** @var string */
    /** @var array */
    public $activePath = [];
    /*** @var Page[] */
    protected $pages = [];
    /** @var array */
    protected $htmlListElements = [];
    /** @var TagDependency */
    protected $tagDependency;


    public function init(): void
    {
        $this->loadPages();
        $this->createHtmlElements();
        $this->tagDependency = new TagDependency(['tags' => [Page::CACHE_TAG_NAME]]);

    }

    protected function loadPages(): void
    {

        $cacheKey = "pagesByParent{$this->parentId}";
        $this->pages = Yii::$app->cache->get($cacheKey);
        if ($this->pages === false) {
            $this->pages = Page::find()
                ->byLang($this->lang)
                ->active()
                ->orderBy('norder')
                ->andWhere(['parent_id' => $this->parentId])
                ->all();
            Yii::$app->cache->set($cacheKey, $this->pages, 0, new TagDependency(['tags' => [Page::CACHE_TAG_NAME]]));
        }
    }

    protected function createHtmlElements(): void
    {
        foreach ($this->pages as $page) {

            $htmlLink = Html::a($page->title_menu, $page->getUrl(), [
                'class' => in_array($page->id, $this->activePath) ? $this->activeElementCssClass : NULl
            ]);

            $cacheKey = "pageChild{$page->id}";
            $children = Yii::$app->cache->get($cacheKey);

            if ($children === false) {
                $children = $page->child;
                Yii::$app->cache->set($cacheKey, $children, 0, new TagDependency(['tags' => [Page::CACHE_TAG_NAME]]));
            }

            if ($children)
                $htmlLink .= self::widget([
                    'lang' => $this->lang,
                    'parentId' => $page->id,
                    'activePath' => $this->activePath,
                    'ulCssClass' => $this->childUlCssClass,
                    'childUlCssClass' => $this->childUlCssClass,
                    'activeElementCssClass' => $this->activeElementCssClass,
                    'ulIsActive' => in_array($page->id, $this->activePath)
                ]);

            $this->htmlListElements[] = Html::tag('li', $htmlLink, [
                'class' => in_array($page->id, $this->activePath) ? $this->activeElementCssClass : NULl
            ]);
        }
    }

    public
    function run(): string
    {
        $finalUlCssClass = $this->ulCssClass;
        if ($this->ulIsActive)
            $finalUlCssClass .= ' ' . $this->activeElementCssClass;
        return Html::tag('ul', implode($this->htmlListElements), [
            'class' => $finalUlCssClass
        ]);
    }

}
