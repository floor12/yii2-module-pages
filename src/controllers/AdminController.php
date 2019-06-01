<?php


namespace floor12\pages\controllers;


use floor12\editmodal\DeleteAction;
use floor12\editmodal\EditModalAction;
use floor12\editmodal\IndexAction;
use floor12\pages\logic\PageUpdate;
use floor12\pages\models\Page;
use floor12\pages\models\PageFilter;
use Yii;
use yii\web\Controller;

class AdminController extends Controller
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = Yii::$app->getModule('pages')->layoutAdmin;
        parent::init();
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'model' => PageFilter::class
            ],
            'form' => [
                'class' => EditModalAction::class,
                'model' => Page::class,
                'logic' => PageUpdate::class,
                'container' => '#pages',
                'view' => '@vendor/floor12/yii2-module-pages/src/views/page/_form.php'
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'model' => Page::class,
                'container' => '#pages',
                'message' => 'Страница удалена',
            ]
        ];
    }
}