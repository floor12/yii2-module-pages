<?php

use yii\db\Migration;

/**
 * Class m220529_074959_page_url
 */
class m220529_074959_page_url extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('page_url', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'url' => $this->string()->notNull(),
        ]);

        $this->createIndex('page_url-page_id', 'page_url', 'page_id');
        $this->createIndex('page_url-created', 'page_url', 'created_at');

        $this->addForeignKey('fk-page_url-page_id', 'page_url', 'page_id', 'page', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('page_url');
    }
}
