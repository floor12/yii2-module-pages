<?php

use yii\db\Migration;

/**
 * Class m180403_114045_init
 */
class m181017_114045_alter_page extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn("{{%page}}", 'index_params', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn("{{%page}}", 'index_params');
    }

}
