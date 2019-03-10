<?php

namespace floor12\pages\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for Page.
 *
 * @see Page
 */
class PageQuery extends ActiveQuery
{
    /**
     * return PageQuery
     */
    public function active(){
        $this->andWhere(['status'=>PageStatus::ACTIVE]);
    }

    /**
     * return PageQuery
     */
    public function visible(){
        $this->andWhere(['menu' => 1]);
    }

    /**
     * @inheritdoc
     * @return Page[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Page|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
