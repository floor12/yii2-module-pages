<?php

use yii\db\Migration;

/**
 * Class m230919_231044_add_page_params
 */
class m230919_231044_add_page_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('page', 'page_params', $this->json()->null());
        try {
            $pages = \floor12\pages\models\Page::find()->all();
            foreach ($pages as $page) {
                if (!$page->index_params) {
                    continue;
                }
                $params = [];
                foreach (explode(';', $page->index_params) as $param) {
                    $parsed = explode('=', $param);
                    if (count($parsed) != 2)
                        continue;
                    list($name, $value) = $parsed;
                    $params[$name] = $value;
                }
                $page->page_params = $params;
                $page->save();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('page', 'page_params');
    }
}