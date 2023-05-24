<?php


namespace floor12\pages\components;


use floor12\pages\models\Page;
use Yii;

class CurrentPagePath
{
    /** @var array */
    protected $pageIds = [];

    /**
     * @param int $pageId
     * @return array
     */
    public function getAsArray(int $pageId): array
    {
        $key = "pagePathCache{$pageId}";
        $result = Yii::$app->cache->get($key);
        if ($result == null) {
            $this->addParent($pageId);
            $result = $this->pageIds;
            Yii::$app->cache->set($key, $result, 600);
        }
        return $result;
    }

    /**
     * @param int $pageId
     */
    protected function addParent(int $pageId): void
    {
        $this->pageIds[] = $pageId;
        $page = Page::findOne($pageId);
        if (!empty($page) && $page->parent_id)
            $this->addParent($page->parent_id);
    }


}
