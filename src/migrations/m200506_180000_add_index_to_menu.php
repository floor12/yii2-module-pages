<?php

use yii\db\Migration;

/**
 * Class m200506_180000_add_index_to_menu
 */
class m200506_180000_add_index_to_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('page-menu', '{{%page}}', 'menu');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('page-menu', '{{%page}}');
    }

}
