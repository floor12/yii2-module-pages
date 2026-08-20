<?php

namespace floor12\pages\controllers;

use floor12\files\logic\FileCreateFromInstance;
use floor12\files\models\File;
use floor12\pages\components\ApiIdentity;
use floor12\pages\logic\PageOrderChanger;
use floor12\pages\logic\PageUpdate;
use floor12\pages\models\Page;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * REST API для удалённого управления страницами модуля pages и привязанными к ним файлами.
 * Авторизация - заголовок "Authorization: Bearer <token>", список валидных токенов задаётся
 * в конфиге хоста через Module::$apiTokens. Не использует Yii::$app->user / RBAC - токен
 * даёт доступ только к сущностям этого модуля (страницы + файлы), не ко всей БД приложения.
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    private $pageModel;

    public function init()
    {
        $this->pageModel = Yii::$app->getModule('pages')->pageModel;
        parent::init();
    }

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'structure' => ['get'],
                    'pages' => ['get'],
                    'page' => ['get', 'post', 'delete'],
                    'page-update' => ['post'],
                    'page-move' => ['post'],
                    'files' => ['get'],
                    'file-upload' => ['post'],
                    'file' => ['delete'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->checkToken();
        return parent::beforeAction($action);
    }

    protected function checkToken()
    {
        $tokens = Yii::$app->getModule('pages')->apiTokens;
        $header = Yii::$app->request->getHeaders()->get('Authorization', '');

        if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches)) {
            throw new UnauthorizedHttpException('Missing bearer token.');
        }

        $token = $matches[1];
        foreach ((array)$tokens as $validToken) {
            if (is_string($validToken) && $validToken !== '' && hash_equals($validToken, $token)) {
                return;
            }
        }

        throw new UnauthorizedHttpException('Invalid API token.');
    }

    /**
     * Языки, дерево страниц и список зарегистрированных "компонентов" (actionsIndex/actionsView).
     */
    public function actionStructure()
    {
        $rows = $this->pageModel::find()
            ->select(['id', 'parent_id', 'key', 'path', 'title', 'title_menu', 'lang', 'status', 'menu', 'link', 'index_action', 'view_action', 'norder'])
            ->orderBy('parent_id, norder')
            ->asArray()
            ->all();

        return [
            'languages' => $this->pageModel::find()->select('lang')->distinct()->column(),
            'components' => [
                'index_actions' => Yii::$app->getModule('pages')->actionsIndex,
                'view_actions' => Yii::$app->getModule('pages')->actionsView,
            ],
            'tree' => $this->buildTree($rows),
        ];
    }

    private function buildTree(array $rows, $parentId = 0): array
    {
        $branch = [];
        foreach ($rows as $row) {
            if ((int)$row['parent_id'] !== (int)$parentId)
                continue;
            $row['children'] = $this->buildTree($rows, $row['id']);
            $branch[] = $row;
        }
        return $branch;
    }

    /**
     * Плоский список страниц с фильтрами по lang/parent_id/status.
     */
    public function actionPages()
    {
        $query = $this->pageModel::find();

        if (($lang = Yii::$app->request->get('lang')) !== null)
            $query->andWhere(['lang' => $lang]);

        if (($parentId = Yii::$app->request->get('parent_id')) !== null)
            $query->andWhere(['parent_id' => $parentId]);

        if (($status = Yii::$app->request->get('status')) !== null)
            $query->andWhere(['status' => $status]);

        return [
            'pages' => array_map([$this, 'pageToArray'], $query->orderBy('parent_id, norder')->all()),
        ];
    }

    /**
     * GET - карточка страницы (?id=). POST - создание (JSON body). DELETE - удаление (?id=).
     */
    public function actionPage()
    {
        return match (Yii::$app->request->method) {
            'GET' => $this->getPage(),
            'POST' => $this->createPage(),
            'DELETE' => $this->deletePage(),
        };
    }

    private function findPageOr404($id): Page
    {
        $model = $this->pageModel::findOne($id);
        if (!$model)
            throw new NotFoundHttpException("Page {$id} not found.");
        return $model;
    }

    private function getPage()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('id'));
        return ['page' => $this->pageToArray($model)];
    }

    private function createPage()
    {
        $model = new $this->pageModel;
        $data = $this->readJsonBody();

        $identity = new ApiIdentity(Yii::$app->getModule('pages')->apiUserId);
        $logic = Yii::createObject(PageUpdate::class, [$model, ['Page' => $data], $identity]);

        if (!$logic->execute())
            throw new BadRequestHttpException('Unable to create page: ' . json_encode($model->errors));

        return ['page' => $this->pageToArray($model)];
    }

    private function deletePage()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('id'));

        if (!$model->delete())
            throw new BadRequestHttpException('Unable to delete page.');

        return ['result' => 'success'];
    }

    /**
     * Обновление существующей страницы (?id= + JSON body с изменяемыми полями).
     */
    public function actionPageUpdate()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('id'));
        $data = $this->readJsonBody();

        $identity = new ApiIdentity(Yii::$app->getModule('pages')->apiUserId);
        $logic = Yii::createObject(PageUpdate::class, [$model, ['Page' => $data], $identity]);

        if (!$logic->execute())
            throw new BadRequestHttpException('Unable to update page: ' . json_encode($model->errors));

        return ['page' => $this->pageToArray($model)];
    }

    /**
     * Реордер страницы среди соседей. mode: 0 - вверх, 1 - вниз (см. logic\PageOrderChanger).
     */
    public function actionPageMove()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('id'));

        $modeParam = Yii::$app->request->get('mode');
        if ($modeParam === null || !in_array((int)$modeParam, [PageOrderChanger::MOVE_UP, PageOrderChanger::MOVE_DOWN], true))
            throw new BadRequestHttpException('mode must be 0 (up) or 1 (down).');

        Yii::createObject(PageOrderChanger::class, [$model, (int)$modeParam])->execute();

        return ['result' => 'success'];
    }

    /**
     * Список файлов, привязанных к странице (поля banner/images/files из FileBehaviour).
     */
    public function actionFiles()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('page_id'));

        $files = File::find()
            ->where(['object_id' => $model->id, 'class' => get_class($model)])
            ->orderBy('field, ordering')
            ->all();

        return ['files' => array_map([$this, 'fileToArray'], $files)];
    }

    /**
     * Загрузка нового файла (multipart, поле "file") и привязка его к странице
     * в один из файловых атрибутов: banner / images / files.
     */
    public function actionFileUpload()
    {
        $model = $this->findPageOr404(Yii::$app->request->get('page_id'));
        $attribute = Yii::$app->request->get('attribute');

        if (!in_array($attribute, ['banner', 'images', 'files'], true))
            throw new BadRequestHttpException('attribute must be one of: banner, images, files.');

        $uploadedFile = UploadedFile::getInstanceByName('file');
        if (!$uploadedFile)
            throw new BadRequestHttpException('No file uploaded under field "file".');

        $identity = new ApiIdentity(Yii::$app->getModule('pages')->apiUserId);
        $file = (new FileCreateFromInstance($uploadedFile, [
            'attribute' => $attribute,
            'modelClass' => get_class($model),
        ], $identity))->execute();

        $existingIds = File::find()
            ->where(['object_id' => $model->id, 'field' => $attribute, 'class' => get_class($model)])
            ->select('id')
            ->column();
        $existingIds[] = $file->id;

        $model->{$attribute . '_ids'} = $existingIds;
        if (!$model->save())
            throw new BadRequestHttpException('Unable to attach file to page: ' . json_encode($model->errors));

        return ['file' => $this->fileToArray($file)];
    }

    public function actionFile()
    {
        $file = File::findOne(Yii::$app->request->get('id'));
        if (!$file || $file->class !== $this->pageModel)
            throw new NotFoundHttpException('File not found.');

        if (!$file->delete())
            throw new BadRequestHttpException('Unable to delete file.');

        return ['result' => 'success'];
    }

    private function readJsonBody(): array
    {
        $raw = Yii::$app->request->getRawBody();
        if ($raw === '')
            return [];

        $data = json_decode($raw, true);
        if (!is_array($data))
            throw new BadRequestHttpException('Request body must be valid JSON.');

        return $data;
    }

    private function pageToArray(Page $model): array
    {
        $data = $model->toArray([
            'id', 'parent_id', 'lang', 'status', 'menu', 'key', 'path',
            'title', 'title_menu', 'title_seo', 'description_seo', 'keywords_seo',
            'content', 'announce', 'link', 'index_action', 'view_action', 'page_params',
            'layout', 'use_purifier', 'menu_css_class', 'norder', 'created', 'updated',
        ]);
        $data['url'] = $model->getUrl();
        return $data;
    }

    private function fileToArray(File $file): array
    {
        return [
            'id' => $file->id,
            'field' => $file->field,
            'title' => $file->title,
            'filename' => $file->filename,
            'content_type' => $file->content_type,
            'size' => $file->size,
            'alt' => $file->alt,
            'ordering' => $file->ordering,
            'href' => $file->getHref(),
        ];
    }
}
