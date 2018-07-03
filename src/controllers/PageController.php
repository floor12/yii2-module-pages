<?php

/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 15.02.2017
 * Time: 11:37
 */

namespace floor12\pages\controllers;

use floor12\pages\logic\PageOrderChanger;
use floor12\pages\logic\PageUpdate;
use floor12\pages\components\Summernote;
use floor12\pages\Page;
use \Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use floor12\pages\logic\PageBreadcrumbs;
use yii\web\NotFoundHttpException;


class PageController extends \yii\web\Controller
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = Yii::$app->getModule('pages')->layout;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'sitemap'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['form', 'imageupload', 'delete', 'move'],
                        'allow' => true,
                        'roles' => [Yii::$app->getModule('pages')->editRole],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['delete'],
                    'move' => ['post'],
                ],
            ],
        ];
    }

    public function actionMove()
    {
        $model = Page::findOne(\Yii::$app->request->post('id'));
        if (!$model)
            throw new NotFoundHttpException();

        $mode = \Yii::$app->request->post('mode');

        Yii::createObject(PageOrderChanger::class, [$model, $mode])->execute();
    }

    public function actionImageupload()
    {
        Summernote::summerUpload();
    }

    public function actionView($path)
    {
        if (!preg_match('/^[\/a-z0-9-]+$/', $path, $matches)) {
            $model = Page::find()->where(['path' => $path, 'status' => Page::STATUS_ACTIVE])->one();
            if ($model)
                return $this->redirect('/' . $model->path . '.html', 301);
            else
                throw new \yii\web\NotFoundHttpException('Запрашиваемый материал не найден на сайте.');
        }


        // этот интересный кусок кода нужен чтобы сначала обеспечить проверку может ли быть последняя часть урла ключом для подключаемого экшена
        $page = Page::findOne(['path' => $path]);


        if (!$page) {
            $pathExploded = explode('/', $path);


            $key = $pathExploded[sizeof($pathExploded) - 1];
            $pathWithoutLastPart = str_replace("/" . $pathExploded[sizeof($pathExploded) - 1], '', $path);

            $page = Page::findOne(['path' => $pathWithoutLastPart]);


            if (!$page || ($page->status == Page::STATUS_DISABLE && !Yii::$app->getModule('pages')->adminMode()))
                throw new NotFoundHttpException();

            if (!$page || !$page->view_action || $page->status)
                throw new \yii\web\NotFoundHttpException('Запрашиваемый материал не найден на сайте.');

            if ($page->view_controller && $page->view_action) {
                $name = strtolower(str_replace('Controller', '', (new \ReflectionClass($page->index_controller))->getShortName()));
                $controller = new $page->index_controller($name, Yii::$app);
                $this->getView()->params['breadcrumbs'] = Yii::createObject(PageBreadcrumbs::class, [$page])->makeBreadcrumbsItems();
                $this->getView()->params['currentPage'] = $page;
                return $controller->{$page->view_action}($key, $page->id);
            }
        }


        if (!$page || ($page->status == Page::STATUS_DISABLE && !Yii::$app->getModule('pages')->adminMode()))
            throw new NotFoundHttpException();


        Yii::$app->getModule('pages')->currentPageId = $page->id;

        $this->getView()->params['breadcrumbs'] = Yii::createObject(PageBreadcrumbs::class, [$page])->makeBreadcrumbsItems();
        $this->getView()->params['currentPage'] = $page;

        Yii::$app->view->title = $page->title_seo;

        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $page->description_seo
        ]);

        Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $page->keywords_seo
        ]);

        Yii::$app->opengraph->title = $page->title;
        Yii::$app->opengraph->type = 'article';
        Yii::$app->opengraph->description = $page->description_seo;
        Yii::$app->opengraph->twitter->card = "summary";
        Yii::$app->opengraph->twitter->site = Yii::$app->opengraph->site_name;
        Yii::$app->opengraph->twitter->title = Yii::$app->opengraph->title;
        Yii::$app->opengraph->twitter->domain = "https://prtc.travel";
        Yii::$app->opengraph->twitter->description = Yii::$app->opengraph->description;

        if ($page->index_controller && $page->index_action) {
            $name = strtolower(str_replace('Controller', '', (new \ReflectionClass($page->index_controller))->getShortName()));
            $controller = new $page->index_controller($name, Yii::$app);
            return $controller->{$page->index_action}($page);
        }


        return $this->render('view', ['model' => $page]);
    }


    public function actions()
    {
        return [
            'form' => [
                'class' => \floor12\editmodal\EditModalAction::className(),
                'model' => Page::className(),
                'logic' => PageUpdate::class,
                'message' => 'Страница сохранена'
            ],
            'delete' => [
                'class' => \floor12\editmodal\DeleteAction::className(),
                'model' => Page::className(),
                'message' => 'Страница удалена'
            ],
        ];
    }
}