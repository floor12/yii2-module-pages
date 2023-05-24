<?php

use yii\db\Migration;

/**
 * Class m220529_193839_add_link_to_page
 */
class m220529_193839_add_link_to_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('page', 'link', $this->string());
        $this->alterColumn('page', 'key', $this->string()->null());
        $this->alterColumn('page', 'path', $this->string()->null());
        $this->alterColumn('page', 'title_seo', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220529_193839_add_link_to_page cannot be reverted.\n";

        return false;
    }
}
