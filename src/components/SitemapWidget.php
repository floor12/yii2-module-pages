<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 22.04.2018
 * Time: 10:54
 */

namespace floor12\pages\components;

use floor12\pages\models\Page;
use Yii;
use yii\base\Widget;

class SitemapWidget extends Widget
{
    /**
     * @var array
     */
    public $links = [];
    /**
     * @var string
     */
    public $protocol = 'http://';
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var string
     */
    protected $hostnameWithProtocol;
    /**
     * @var array
     */
    protected $cachedData;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (empty($this->hostname))
            $this->hostname = Yii::$app->request->hostName;

        $this->hostnameWithProtocol = $this->protocol . $this->hostname;

        $this->cachedData = Yii::$app->cache->get('sitemap');

        if ($this->cachedData === false) {

            $this->cachedData[] = ['url' => '/', 'priority' => 1];

            $pages = Page::find()
                ->active()
                ->andWhere(['=', 'link', ''])
                ->orderBy('path')
                ->all();

            if ($pages) foreach ($pages as $page) {
                if ($page->lang == 'ru' && $page->path == '/')
                    continue;
                $this->cachedData[$page->url] = ['url' => $page->url, 'priority' => 0.5, 'lastmod' => date("c", $page->updated)];
            }

            $this->cachedData = array_merge($this->cachedData, $this->links);

//            Сохраняем значение $data в кэше . Данные можно получить в следующий раз .
            Yii::$app->cache->set('sitemap', $this->cachedData, 300);
        }
    }

    public function run()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');
        return $this->render('sitemap', ['urls' => $this->cachedData, 'hostnameWithProtocol' => $this->hostnameWithProtocol]);
    }
}
