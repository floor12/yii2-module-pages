<?php

use yii\db\Migration;

/**
 * Class m200320_105458_add_layout_to_page
 */
class m200320_105458_add_layout_to_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%page}}', 'layout', $this->string(255)->null()->comment('layout'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%page}}', 'layout');
    }

}
