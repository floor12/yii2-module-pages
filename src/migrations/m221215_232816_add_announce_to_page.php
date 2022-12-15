<?php

use yii\db\Migration;

/**
 * Class m221215_232816_add_announce_to_page
 */
class m221215_232816_add_announce_to_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('page', 'announce', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('page', 'announce');
    }
}