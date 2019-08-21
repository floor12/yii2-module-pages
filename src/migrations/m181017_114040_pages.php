<?php

use yii\db\Migration;

/**
 * Class m180403_114045_init
 */
class m181017_114040_pages extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        # Pages  --------------------------------------------------------------------------------------------------
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Скрыть'),
            'menu' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Показывать в меню'),
            'created' => $this->integer()->notNull()->comment('Время создания'),
            'updated' => $this->integer()->notNull()->comment('Время обновления'),
            'create_user_id' => $this->integer()->null()->comment('Создал'),
            'update_user_id' => $this->integer()->null()->comment('Обновил'),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('Родительский раздел'),
            'title' => $this->string(255)->null()->comment('Заголовок страницы'),
            'title_menu' => $this->string(255)->notNull()->comment('Заголовок меню'),
            'title_seo' => $this->string(255)->notNull()->comment('Title страницы'),
            'description_seo' => $this->string(400)->null()->comment('Meta Description'),
            'keywords_seo' => $this->string(400)->null()->comment('Meta keywords'),
            'key' => $this->string(400)->notNull()->comment('Ключевое слово для URL'),
            'norder' => $this->integer(11)->defaultValue(0)->notNull()->comment('Порядок'),
            'path' => $this->string(255)->null()->comment('Полный путь'),
            'content' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null()->comment('Тело страницы'),
            'index_controller' => $this->string(255)->null()->comment('Index Controller'),
            'index_action' => $this->string(255)->null()->comment('Index Action'),
            'view_controller' => $this->string(255)->null()->comment('View Controller'),
            'view_action' => $this->string(255)->null()->comment('View Action'),

        ], $tableOptions);

        $this->createIndex("idx-page-status", "{{%page}}", "status");
        $this->createIndex("idx-page-key", "{{%page}}", "key");
        $this->createIndex("idx-page-path", "{{%page}}", "path");
        $this->createIndex("idx-page-parent_id", "{{%page}}", "parent_id");
        $this->createIndex("idx-page-norder", "{{%page}}", "norder");

        $this->createIndex("idx-page-created", "{{%page}}", "created");
        $this->createIndex("idx-page-updated", "{{%page}}", "updated");
        $this->createIndex("idx-page-create_user_id", "{{%page}}", "create_user_id");
        $this->createIndex("idx-page-update_user_id", "{{%page}}", "update_user_id");

        $this->addForeignKey("fk-page-creator", '{{%page}}', "create_user_id", "{{%user}}", "id", "SET NULL");
        $this->addForeignKey("fk-page-updator", '{{%page}}', "update_user_id", "{{%user}}", "id", "SET NULL");
    }

    /**
     * @inheritdoc
     */

    public function safeDown()
    {
        $this->dropTable("{{%page}}");
    }

}
