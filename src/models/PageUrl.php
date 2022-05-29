<?php

namespace floor12\pages\models;

use Yii;

/**
 * This is the model class for table "page_url".
 *
 * @property int $id
 * @property int $page_id
 * @property int $created_at
 * @property int $url
 *
 * @property Page $page
 */
class PageUrl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'page_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rles()
    {
        return [
            [['page_id', 'created_at', 'url'], 'required'],
            [['page_id', 'created_at'], 'integer'],
            [['url'], 'string'],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'created_at' => 'Created At',
            'url' => 'Url',
        ];
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }
}
