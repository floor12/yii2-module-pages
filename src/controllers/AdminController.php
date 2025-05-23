<?php


namespace floor12\pages\controllers;


use floor12\editmodal\DeleteAction;
use floor12\editmodal\EditModalAction;
use floor12\editmodal\IndexAction;
use floor12\pages\components\GptHelper;
use floor12\pages\logic\PageOrderChanger;
use floor12\pages\logic\PageUpdate;
use floor12\pages\models\Page;
use floor12\pages\models\PageFilter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AdminController extends Controller
{
    public $formView;
    public $pageModel;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Yii::$app->getModule('pages')->editRole],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['delete'],
                    'form' => ['get', 'post'],
                    'index' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = Yii::$app->getModule('pages')->layoutAdmin;
        $this->formView = Yii::$app->getModule('pages')->viewForm;
        $this->pageModel = Yii::$app->getModule('pages')->pageModel;
        parent::init();
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

    public function actionSort()
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);

        foreach ($data['pages'] as $row) {
            if ($row['id'] ?? null) {
                $page = Page::findOne($row['id']);
                unset($row['id']);
                if (!$page)
                    continue;
                $logic = new PageUpdate($page, ['Page' => $row], Yii::$app->user->getIdentity());
                $logic->execute();
            }
        }
    }

    public function actionMakeMeta()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $content = Yii::$app->request->post('pageContent');
        $resp = GptHelper::MakeMetaTags($content);
        return json_decode($resp, true);
    }

    public function actionMakeContent()
    {
        $query = Yii::$app->request->post('query');
        $lang = Yii::$app->request->post('lang');
        $resp = GptHelper::MakeContent($query, $lang);
        file_put_contents('/app/runtime/' . time() . '.html', $resp);
        return $resp;
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'model' => PageFilter::class,
            ],
            'form' => [
                'class' => EditModalAction::class,
                'model' => $this->pageModel::className(),
                'logic' => PageUpdate::class,
                'container' => '#pages',
                'view' => $this->formView,
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'model' => $this->pageModel::className(),
                'container' => '#pages',
                'message' => 'Страница удалена',
            ]
        ];
    }
}