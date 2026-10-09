<?php

use floor12\pages\models\Page;
use yii\caching\TagDependency;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m261009_120000_normalize_page_keys
 *
 * Убирает лишние слэши из page.key и page.path (например, key "/foo" давал path "parent//foo",
 * что приводило к бесконечному редиректу через историю URL).
 */
class m261009_120000_normalize_page_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $rows = (new Query())
            ->select(['id', 'key', 'path', 'lang'])
            ->from('page')
            ->all($this->db);

        foreach ($rows as $row) {
            $key = Page::normalizePath($row['key']);
            $path = Page::normalizePath($row['path']);

            if ($key === $row['key'] && $path === $row['path'])
                continue;

            $collision = (new Query())
                ->from('page')
                ->where(['path' => $path, 'lang' => $row['lang']])
                ->andWhere(['<>', 'id', $row['id']])
                ->exists($this->db);

            if ($collision) {
                echo "    > page #{$row['id']}: path '{$path}' ({$row['lang']}) already taken, skipped\n";
                continue;
            }

            $this->update('page', ['key' => $key, 'path' => $path], ['id' => $row['id']]);
        }

        if (Yii::$app->has('cache'))
            TagDependency::invalidate(Yii::$app->cache, Page::CACHE_TAG_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        echo "    > m261009_120000_normalize_page_keys: data normalization is not reverted\n";
    }
}
