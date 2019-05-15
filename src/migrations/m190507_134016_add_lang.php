<?php

use floor12\pages\models\Page;
use yii\db\Migration;

/**
 * Class m190507_134016_add_lang
 */
class m190507_134016_add_lang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Page::tableName(), 'lang', $this
            ->string(3)
            ->notNull()
            ->defaultValue('ru')
            ->defaultValue('Язык страницы')
        );

        $this->createIndex('idx-page-lang', Page::tableName(), 'lang');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Page::tableName(), 'lang');
    }


}
