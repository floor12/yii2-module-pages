<?php

use yii\db\Migration;

/**
 * Class m220214_093008_add_use_purifier_to_page
 */
class m220214_093008_add_use_purifier_to_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('page', 'use_purifier', $this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('page', 'use_purifier');
    }

}
