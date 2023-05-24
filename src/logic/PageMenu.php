<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 19:02
 */

namespace floor12\pages\logic;

use floor12\pages\models\Page;

class PageMenu
{

    private $_model;
    private $_items = [];
    private $_pages = [];

    public function __construct(Page $model)
    {
        $this->_model = $model;
        $this->_pages = Page::find()
            ->where(['parent_id' => $this->_model->parent_id, 'menu' => Page::SHOW_IN_MENU])
            ->orderBy('norder')
            ->all();
    }

    public function makeItems(): array
    {
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                $this->_items[] = [
                    'href' => $page->url,
                    'name' => $page->title_menu,
                    'description' => $page->title_seo,
                ];
            }


        return $this->_items;
    }

}