<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 11.04.2018
 * Time: 13:05
 */

namespace floor12\pages\logic;

use floor12\pages\Page;

class PageOrderChanger
{

    const MOVE_UP = 0;
    const MOVE_DOWN = 1;

    private $_model;
    private $_mode;


    public function __construct(Page $model, int $mode)
    {
        $this->_model = $model;
        $this->_mode = $mode;
    }

    public function execute()
    {
        if ($this->_model->norder < 2)
            return true;

        $oldOrder = $this->_model->norder;

        if ($this->_mode == self::MOVE_UP) {
            $this->_model->norder--;
        } else {
            $this->_model->norder++;
        }
        $obj = Page::find()->where(['norder' => $this->_model->norder, 'parent_id' => (int)$this->_model->parent_id])->one();

        if ($obj) {
            $obj->norder = $oldOrder;
            $obj->save();
        }
        $this->_model->save();
        $this->reorder();
    }

    public function reorder()
    {
        $rows = Page::find()->where('parent_id=:id', ['id' => $this->_model->parent_id])->orderBy('norder')->all();
        if ($rows)
            foreach ($rows as $key => $row) {
                $row->norder = ++$key;
                $row->save();
            }
    }
}