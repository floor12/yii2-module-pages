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
     * @param string $lang
     * @return PageQuery
     */
    public function byLang(string $lang)
    {
        return $this->andWhere(['lang' => $lang]);
    }


    /**
     * @return PageQuery
     */
    public function root()
    {
        return $this->andWhere(['parent_id' => 0]);
    }

    /**
     * return PageQuery
     */
    public function active()
    {
        return $this->andWhere(['status' => PageStatus::ACTIVE]);
    }

    /**
     * return PageQuery
     */
    public function visible()
    {
        return $this->andWhere(['menu' => 1]);
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

    public function dropDown(): array
    {
        return $this
            ->orderBy('title_menu')
            ->select('title_menu')
            ->indexBy('id')
            ->column();
    }
}
