<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 18:31
 */

namespace floor12\pages\logic;

use floor12\pages\models\Page;

class PageBreadcrumbs
{
    private $_model;
    private $_items = [];

    /**
     * PageBreadcrumbs constructor.
     * @param Page $model
     */
    public function __construct(Page $model)
    {
        $this->_model = $model;
    }

    /**
     * @return array
     */
    public function makeBreadcrumbsItems(): array
    {
        $this->processItem($this->_model);
        $this->_items = array_reverse($this->_items);

        if ($this->_model->view_action)
            $this->_items[$this->_model->url] = $this->_model->title_menu;
        else
            $this->_items[] = $this->_model->title_menu;

        return $this->_items;
    }

    private function processItem(Page $item)
    {
        if ($item->parent) {
            if ($item->parent->menu)
                $this->_items[$item->parent->url] = $item->parent->title_menu;
            else
                $this->_items[] = $item->parent->title_menu;

            if ($item->parent->parent)
                $this->processItem($item->parent);
        }

    }
}