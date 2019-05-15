<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 15:34
 */

namespace floor12\pages\logic;

use floor12\pages\models\Page;
use yii\web\IdentityInterface;

class PageUpdate
{

    private $_model;
    private $_data;
    private $_identity;

    public function __construct(Page $model, array $data, IdentityInterface $identity)
    {
        $this->_model = $model;
        $this->_data = $data;
        $this->_identity = $identity;

        $model->updated = time();
        $model->update_user_id = $this->_identity->getId();

        if ($this->_model->isNewRecord) {
            $this->_model->created = time();
            $this->_model->create_user_id = $this->_identity->getId();

        }

    }

    public function execute()
    {
        $this->_model->load($this->_data);
        $this->_model->path = $this->_model->key;

        if (!$this->_model->lang)
            $this->_model->lang = 'ru';

        if ($this->_model->isNewRecord)
            $this->_model->norder = Page::find()->where(['parent_id' => $this->_model->parent_id])->count() + 1;

        if ($this->_model->parent_id) {
            $parentPath = Page::find()->where(['id' => $this->_model->parent_id])->select('path')->scalar();
            $this->_model->path = $parentPath . "/" . $this->_model->key;
        }


        return $this->_model->save();
    }
}