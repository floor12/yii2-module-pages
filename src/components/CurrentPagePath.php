<?php


namespace floor12\pages\components;


use floor12\pages\models\Page;

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
        $this->addParent($pageId);
        return $this->pageIds;
    }

    /**
     * @param int $pageId
     */
    protected function addParent(int $pageId): void
    {
        $this->pageIds[] = $pageId;
        $page = Page::findOne($pageId);
        if ($page->parent_id)
            $this->addParent($page->parent_id);
    }


}
