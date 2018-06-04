<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 22.04.2018
 * Time: 10:54
 */

namespace floor12\pages\components;

use floor12\pages\Page;
use \Yii;
use yii\base\Widget;

class SitemapWidget extends Widget
{
    public $links = [];
    public $scheme = 'http://';

    private $_cachedData;
    private $_host;

    public function init()
    {

        $this->_host = $this->scheme . Yii::$app->request->hostName;

        $this->_cachedData = Yii::$app->cache->get('sitemap');

        if ($this->_cachedData === false) {

            $this->_cachedData[] = ['url' => '/', 'priority' => 1];

            $pages = Page::find()->where(['status' => Page::STATUS_ACTIVE])->orderBy('path')->all();
            if ($pages) foreach ($pages as $page) {
                $this->_cachedData[$page->url] = ['url' =>  $page->url, 'priority' => 0.5, 'lastmod' => date("c", $page->updated)];
            }

            $this->_cachedData = array_merge($this->_cachedData, $this->links);

            // Сохраняем значение $data в кэше. Данные можно получить в следующий раз.
            Yii::$app->cache->set('sitemap', $this->_cachedData, 300);
        }
    }

    public function run()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');
        return $this->render('sitemap', ['urls' => $this->_cachedData, 'host' => $this->_host]);
    }
}