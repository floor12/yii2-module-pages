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
use floor12\files\components\PictureWidget;
use floor12\files\models\File;
use floor12\maps\models\Map;
use floor12\maps\widgets\MapWidget;
use floor12\pages\components\MapYandexWidget;
use floor12\pages\logic\PageBreadcrumbs;
use floor12\pages\logic\PageOrderChanger;
use floor12\pages\logic\PageUpdate;
use floor12\pages\models\Page;
use floor12\pages\models\PageStatus;
use floor12\pages\models\PageUrl;
use floor12\summernote\Summernote;
use floor12\youtube\YoutubeProcessor;
use Yii;
use yii\caching\TagDependency;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;


class PageController extends \yii\web\Controller
{
    private $pageModel;
    private $formView;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = Yii::$app->getModule('pages')->layout;
        $this->formView = Yii::$app->getModule('pages')->viewForm;
        $this->pageModel = Yii::$app->getModule('pages')->pageModel;
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
        $model = $this->pageModel::findOne(\Yii::$app->request->post('id'));
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

        // этот интересный кусок кода нужен чтобы сначала обеспечить проверку может ли быть последняя часть урла ключом для подключаемого экшена
        $page = $this->pageModel::find()
            ->cache(60 * 60, new TagDependency(['tags' => $this->pageModel::CACHE_TAG_NAME]))
            ->where(['path' => $path, 'lang' => Yii::$app->language])
            ->one();

        $pathExploded = explode('/', $path);
        $key = $pathExploded[sizeof($pathExploded) - 1];
        $pathWithoutLastPart = str_replace("/" . $pathExploded[sizeof($pathExploded) - 1], '', $path);

        if (!$page) {

            $page = $this->pageModel::findOne(['path' => $pathWithoutLastPart, 'lang' => Yii::$app->language]);


            if (!$page || ($page->status == PageStatus::DISABLED && !Yii::$app->getModule('pages')->adminMode()))
                return $this->checkUrlLogOrThrow($path, $pathWithoutLastPart, $key);

            if (!$page || !$page->view_action || $page->status)
                return $this->checkUrlLogOrThrow($path, $pathWithoutLastPart, $key, 'Запрашиваемый материал не найден на сайте.');

            if ($page->layout)
                $this->layout = $page->layout;

            if ($page->view_action) {
                list($viewController, $viewAction) = explode('::', $page->view_action);
                if (substr($viewAction, 0, 6) == 'action')
                    $viewAction = substr($viewAction, 6);

                $name = strtolower(str_replace('Controller', '', (new \ReflectionClass($viewController))->getShortName()));
                $controller = new $viewController($name, Yii::$app);
                $this->getView()->params['breadcrumbs'] = Yii::createObject(PageBreadcrumbs::class, [$page])->makeBreadcrumbsItems();
                $this->getView()->params['currentPage'] = $page;
                Yii::$app->getModule('pages')->currentPageId = $page->id;
                return $controller->runAction(strtolower($viewAction), ['key' => $key, 'page' => $page]);
            }
        }


        if (!$page || ($page->status == PageStatus::DISABLED && !Yii::$app->getModule('pages')->adminMode()))
            return $this->checkUrlLogOrThrow($path, $pathWithoutLastPart, $key);

        if ($page->layout)
            $this->layout = $page->layout;

        Yii::$app->getModule('pages')->currentPageId = $page->id;

        $this->getView()->params['breadcrumbs'] = Yii::createObject(PageBreadcrumbs::class, [$page])->makeBreadcrumbsItems();
        $this->getView()->params['currentPage'] = $page;


        Yii::$app->metamaster
            ->setTitle($page->title_seo)
            ->setDescription(strval($page->description_seo))
            ->register(Yii::$app->getView());

        if ($page->index_action) {
            list($indexController, $indexAction) = $indexController = explode('::', $page->index_action);
            if (!$indexAction || !$indexController)
                throw new NotFoundHttpException('Controller or Action name parse error.');
            $name = strtolower(str_replace('Controller', '', (new \ReflectionClass($indexController))->getShortName()));
            $controller = new $indexController($name, Yii::$app);

            if (substr($indexAction, 0, 6) == 'action')
                $indexAction = substr($indexAction, 6);

            $indexParams = [];
            if ($page->index_params) {
                foreach (explode(';', $page->index_params) as $paramRow) {
                    $explodedRow = explode('=', $paramRow);
                    $indexParams[$explodedRow[0]] = $explodedRow[1];
                };
            }
//            echo 1;
//            die();
            return $controller->runAction(strtolower($indexAction), array_merge(['page' => $page], $indexParams));
        }

        $this->parseWidgets($page);

        return $this->render(Yii::$app->getModule('pages')->view, ['model' => $page]);
    }

    private function checkUrlLogOrThrow($path, $pathWithoutLastPart, $key, $error = null)
    {
        if ($oldFullPathUrl = PageUrl::find()
            ->where(['url' => $path])
            ->orderBy('created_at DESC')
            ->one()) {
            return $this->redirect('/' . $oldFullPathUrl->page->path, 301);
        }


        if ($oldFullPathUrl = PageUrl::find()
            ->where(['url' => $pathWithoutLastPart])
            ->orderBy('created_at DESC')
            ->one()) {
            return $this->redirect('/' . $oldFullPathUrl->page->path . '/' . $key, 301);
        }

        throw new NotFoundHttpException($error);
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

        if (preg_match_all('/{{openmap:([\w\%]*)}}/', $page->content, $mapMatches)) {
            foreach ($mapMatches[1] as $key => $mapId) {
                $map = Map::findOne($mapId);
                if ($map)
                    $page->content = str_replace($mapMatches[0][$key], MapWidget::widget(['map' => $map]), $page->content);
            }
        }

        if (preg_match_all('/{{image:\s([a-zA-Z0-9]+),\s*width:\s([0-9%]+),\s*alt:\s([^}]+)}}/', $page->content, $mapMatches)) {
            foreach ($mapMatches[1] as $resultKey => $hash) {
                $widget = PictureWidget::widget([
                    'model' => File::findOne(['hash' => $hash]),
                    'alt' => $mapMatches[3][$resultKey],
                    'width' => $mapMatches[2][$resultKey],
                ]);
                $page->content = str_replace($mapMatches[0][$resultKey], $widget, $page->content);
            }
        }

        $page->content = YoutubeProcessor::process($page->content);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'form' => [
                'class' => EditModalAction::className(),
                'model' => $this->pageModel::className(),
                'logic' => PageUpdate::class,
                'view' => $this->formView,
                'container' => '#pages',
                'message' => 'Страница сохранена'
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'model' => $this->pageModel::className(),
                'message' => 'Страница удалена'
            ],
        ];
    }
}
