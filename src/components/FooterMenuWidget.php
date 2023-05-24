<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 13.04.2018
 * Time: 10:37
 */

namespace floor12\pages\components;


use floor12\pages\models\Page;
use floor12\pages\models\PageMenuVisibility;
use yii\base\Widget;

class FooterMenuWidget extends Widget
{
    /**
     * @var integer
     */
    public $parent_id;
    /**
     * @var string
     */
    public $lang = 'ru';

    /**
     * @var Page[]
     */
    private $_pages = [];

    public function init()
    {

        $this->_pages = Page::find()
            ->where([
                'lang' => $this->lang,
                'parent_id' => $this->parent_id,
                'menu' => PageMenuVisibility::VISIBLE
            ])
            ->orderBy('norder')
            ->with('child')
            ->all();
        parent::init();
    }

    function run()
    {
        $nodes = [];
        if ($this->_pages)
            foreach ($this->_pages as $page) {
                if (strpos('/' . \Yii::$app->request->pathInfo, $page->url) === 0)
                    $page->active = true;

                $subs = [];
                if ($page->child) foreach ($page->child as $sub) {
                    if (strpos('/' . \Yii::$app->request->pathInfo, $sub->url) === 0)
                        $sub->active = true;
                    if ($sub->menu)
                        $subs [] = $this->render('_footerMenuWidget', ['model' => $sub]);
                }
                $nodes[] = $this->render('footerMenuWidget', ['model' => $page, 'subs' => implode("\n", $subs)]);
            }

        return implode("\n", $nodes);
    }
}