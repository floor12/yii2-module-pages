<?php

use floor12\pages\models\Page;
use yii\db\Migration;

/**
 * Class m230607_224922_alter_page
 */
class m230607_224922_alter_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pagesWithIndexAction = Page::find()
            ->andWhere(['is not', 'index_action', null])
            ->all();
        try {
            foreach ($pagesWithIndexAction as $page) {
                if ($page->index_action) {
                    $page->index_action = $page->index_controller . '::' . $page->index_action;
                }
                if ($page->view_action) {
                    $page->view_action = $page->view_controller . '::' . $page->view_action;
                }
                $page->save();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->dropColumn('page', 'index_controller');
        $this->dropColumn('page', 'view_controller');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230607_224922_alter_page cannot be reverted.\n";
        return false;
    }

}
