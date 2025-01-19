<?php

use yii\db\Migration;

/**
 * Class m250119_184803_add_menu_css_to_page
 */
class m250119_184803_add_menu_css_to_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('page', 'menu_css_class', $this->string(255)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('page', 'menu_css_class');
    }
}


