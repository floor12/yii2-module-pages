<?php

/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 15.02.2017
 * Time: 11:37
 */

namespace floor12\pages\controllers;

use floor12\editmodal\DeleteAction;
use floor12\editmodal\EditModalAction;
use floor12\pages\components\MapYandexWidget;
use floor12\pages\logic\PageBreadcrumbs;
use floor12\pages\logic\PageOrderChanger;
use floor12\pages\logic\PageUpdate;
use floor12\pages\models\Page;
use floor12\pages\models\PageStatus;
use floor12\summernote\Summernote;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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

    /**
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMove()
    {
        $model = Page::findOne(\Yii::$app->request->post('id'));
        if (!$model)
            throw new NotFoundHttpException();

        $mode = \Yii::$app->request->post('mode');

        Yii::createObject(PageOrderChanger::class, [$model, $mode])->execute();
    }


    /**
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionImageupload()
    {
        return Summernote::summerUpload();
    }


    /**
     * @param $path
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($path)
    {
        if (!preg_match('/^[\/a-z0-9-]+$/', $path, $matches)) {
            $model = Page::find()->where(['lang' => Yii::$app->language, 'path' => $path, 'status' => PageStatus::ACTIVE])->one();
            if ($model)
                return $this->redirect('/' . $model->path . '.html', 301);
            else
                throw new \yii\web\NotFoundHttpException('Запрашиваемый материал не найден на сайте.');
        }


        // этот интересный кусок кода нужен чтобы сначала обеспечить проверку может ли быть последняя часть урла ключом для подключаемого экшена
        $page = Page::findOne(['path' => $path, 'lang' => Yii::$app->language,]);


        if (!$page) {
            $pathExploded = explode('/', $path);


            $key = $pathExploded[sizeof($pathExploded) - 1];
            $pathWithoutLastPart = str_replace("/" . $pathExploded[sizeof($pathExploded) - 1], '', $path);

            $page = Page::findOne(['path' => $pathWithoutLastPart, 'lang' => Yii::$app->language]);


            if (!$page || ($page->status == PageStatus::DISABLED && !Yii::$app->getModule('pages')->adminMode()))
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


        if (!$page || ($page->status == PageStatus::DISABLED && !Yii::$app->getModule('pages')->adminMode()))
            throw new NotFoundHttpException();


        Yii::$app->getModule('pages')->currentPageId = $page->id;

        $this->getView()->params['breadcrumbs'] = Yii::createObject(PageBreadcrumbs::class, [$page])->makeBreadcrumbsItems();
        $this->getView()->params['currentPage'] = $page;


        Yii::$app->metamaster
            ->setTitle($page->title_seo)
            ->setDescription($page->description_seo)
            ->register(Yii::$app->getView());

        if ($page->index_controller && $page->index_action) {
            $name = strtolower(str_replace('Controller', '', (new \ReflectionClass($page->index_controller))->getShortName()));
            $controller = new $page->index_controller($name, Yii::$app);

            if (substr($page->index_action, 0, 6) == 'action')
                $page->index_action = substr($page->index_action, 6);


            $indexParams = [];
            if ($page->index_params) {
                foreach (explode(';', $page->index_params) as $paramRow) {
                    $explodedRow = explode('=', $paramRow);
                    $indexParams[$explodedRow[0]] = $explodedRow[1];
                };
            }

            return $controller->runAction(strtolower($page->index_action), array_merge(['page' => $page], $indexParams));
        }

        $this->parseWidgets($page);

        return $this->render(Yii::$app->getModule('pages')->view, ['model' => $page]);
    }


    /**
     * @param Page $page
     */
    protected function parseWidgets(Page $page)
    {
        if (preg_match_all('/{{map:([\w\%]*)}}/', $page->content, $mapMatches)) {
            foreach ($mapMatches[1] as $key => $mapKey) {
                $page->content = str_replace($mapMatches[0][$key], MapYandexWidget::widget(['key' => $mapKey]), $page->content);
            }
        }

    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'form' => [
                'class' => EditModalAction::className(),
                'model' => Page::className(),
                'logic' => PageUpdate::class,
                'message' => 'Страница сохранена'
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'model' => Page::className(),
                'message' => 'Страница удалена'
            ],
        ];
    }
}