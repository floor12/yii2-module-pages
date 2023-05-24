<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 15:34
 */

namespace floor12\pages\logic;

use floor12\pages\models\Page;
use floor12\pages\models\PageMenuVisibility;
use floor12\pages\models\PageStatus;
use yii\web\IdentityInterface;
use yii\caching\TagDependency;
use Yii;

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

        if ($this->_model->status == PageStatus::DISABLED) {
            $this->_model->menu = PageMenuVisibility::HIDDEN;
        }

        if ($this->_model->isNewRecord)
            $this->_model->norder = Page::find()->where(['parent_id' => $this->_model->parent_id])->count();


        if ($this->_model->save()) {
            $this->updatePath($this->_model);
            TagDependency::invalidate(Yii::$app->cache, Page::CACHE_TAG_NAME);
            return true;
        }

        return false;
    }

    protected function updatePath(Page $model)
    {
        if ($model->parent_id) {
            $parentPath = Page::find()->where(['id' => $model->parent_id])->select('path')->scalar();
            $model->lang = Page::find()->where(['id' => $model->parent_id])->select('lang')->scalar();
            $model->path = $parentPath . "/" . $model->key;
        } else
            $model->path = $model->key;

        $model->save(false, ['path', 'lang']);

        if (!$model->child)
            return;

        foreach ($model->child as $child)
            $this->updatePath($child);

        return;
    }
}
