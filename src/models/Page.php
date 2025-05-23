<?php

namespace floor12\pages\models;

use floor12\files\components\FileBehaviour;
use floor12\files\models\File;
use floor12\pages\components\Annotations;
use floor12\pages\components\PurifyBehavior;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UrlManager;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property int $status Скрыть
 * @property int $menu Показывать в меню
 * @property int $created Время создания
 * @property int $updated Время обновления
 * @property int $create_user_id Создал
 * @property int $update_user_id Обновил
 * @property int $parent_id Родительский раздел
 * @property string $title Заголовок страницы
 * @property string $title_seo Title страницы
 * @property string $title_menu Title меню
 * @property string $description_seo Meta Description
 * @property string $keywords_seo Meta keywords
 * @property string $key Ключевое слово для URL
 * @property int $norder Порядок
 * @property string $path Полный путь
 * @property string $content Тело страницы
 * @property string $url Url страницы
 * @property string $layout Шаблон
 * @property string $menu_css_class Шаблон
 * @property boolean $active Активна ли данная страницы
 * @property string $index_action Экшн индекса
 * @property string $index_params Параметры экшена индекса
 * @property string $view_action Экшн для просмотра объекта
 * @property string $lang Язык страницы
 * @property string $link
 * @property boolean $use_purifier Очищать html
 * @property string $announce Анонс
 * @property array $page_params
 *
 * @property User $creator
 * @property User $updator
 * @property Page $parent
 * @property Page[] $child
 * @property Page[] $childVisible
 * @property array $child_ids
 * @property File $banner
 * @property File[] $images
 * @property File[] $files
 */
class Page extends ActiveRecord
{
    const CACHE_TAG_NAME = "pages";

    public $active = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     * @return PageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['key', 'trim'],
            [['status', 'created', 'updated', 'create_user_id', 'update_user_id', 'parent_id', 'norder', 'menu'], 'integer'],
            [['created', 'updated', 'title_menu'], 'required'],
            [['title_seo', 'key', 'title_menu'], 'required', 'when' => function ($model) {
                return $this->isLink() === false;
            }],
            [['content', 'link', 'announce'], 'string'],
            [['use_purifier'], 'boolean'],
            ['lang', 'string', 'max' => 3],
            [['title', 'title_seo', 'title_menu', 'path', 'index_params', 'view_action', 'index_action'], 'string', 'max' => 255],
            [['description_seo', 'keywords_seo', 'key'], 'string', 'max' => 400],
            [['menu_css_class', 'layout'], 'string', 'max' => 255],
            ['status', 'in', 'range' => [PageStatus::ACTIVE, PageStatus::DISABLED]],
            ['menu', 'in', 'range' => [PageMenuVisibility::VISIBLE, PageMenuVisibility::HIDDEN]],
            [['create_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->getModule('pages')->userModel, 'targetAttribute' => ['create_user_id' => 'id']],
            [['update_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->getModule('pages')->userModel, 'targetAttribute' => ['update_user_id' => 'id']],
            ['key', 'match', 'pattern' => '/^[-a-z0-9\/]*$/', 'message' => 'Ключ URL может состоять только из латинских букв в нижнем регистре, цифр и дефиса.'],
            ['images', 'file', 'maxFiles' => 150, 'extensions' => ['jpeg', 'png', 'jpg', 'svg', 'webp']],
            ['files', 'file', 'maxFiles' => 150],
            ['banner', 'file', 'maxFiles' => 1],
            ['page_params ', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'purify' => [
                'class' => PurifyBehavior::class,
                'attributes' => ['content', 'announce'],
            ],
            'files' => [
                'class' => FileBehaviour::class,
                'attributes' => [
                    'banner',
                    'images' => [
                        'maxWidth' => 3800,
                        'maxHeight' => 3800,
                    ],
                    'files',
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => Yii::t('app.f12.pages', 'Disable page'),
            'created' => Yii::t('app.f12.pages', 'Created'),
            'updated' => Yii::t('app.f12.pages', 'Updated'),
            'create_user_id' => Yii::t('app.f12.pages', 'Create User ID'),
            'update_user_id' => Yii::t('app.f12.pages', 'Update User ID'),
            'parent_id' => Yii::t('app.f12.pages', 'Parent page'),
            'title' => Yii::t('app.f12.pages', 'Title'),
            'title_seo' => Yii::t('app.f12.pages', 'Title SEO'),
            'title_menu' => Yii::t('app.f12.pages', 'Title menu'),
            'description_seo' => Yii::t('app.f12.pages', 'Description SEO'),
            'key' => Yii::t('app.f12.pages', 'URL slug'),
            'norder' => Yii::t('app.f12.pages', 'Order'),
            'path' => Yii::t('app.f12.pages', 'Path'),
            'content' => Yii::t('app.f12.pages', 'Content'),
            'layout' => Yii::t('app.f12.pages', 'Layout'),
            'menu' => Yii::t('app.f12.pages', 'Show in menu'),
            'view_action' => Yii::t('app.f12.pages', 'Additional component'),
            'index_action' => Yii::t('app.f12.pages', 'Main component'),
            'index_params' => 'Index Params',
            'lang' => Yii::t('app.f12.pages', 'Language'),
            'files' => Yii::t('app.f12.pages', 'Files'),
            'images' => Yii::t('app.f12.pages', 'Images'),
            'banner' => Yii::t('app.f12.pages', 'Page cover'),
            'use_purifier' => Yii::t('app.f12.pages', 'Use Purifier'),
            'menu_css_class' => Yii::t('app.f12.pages', 'Custom menu CSS class'),
        ];
    }

    /**
     * @return string Full uri of current page.
     */
    public function getUrl()
    {
        if ($this->isLink()) {
            if (str_starts_with($this->link, 'http')) {
                return $this->link;
            }
            return Yii::$app->urlManager->createAbsoluteUrl($this->link);
        }
        if ($this->path == '/') {
            if (Yii::$app->urlManager::className() == UrlManager::class)
                return '/';
            if (isset(Yii::$app->urlManager->languages[0]) && Yii::$app->urlManager->languages[0] == $this->lang)
                return '/';

            return '/' . $this->lang;
        }

        if (!strip_tags($this->content) && $this->child && !$this->index_action)
            return $this->child[0]->url;

        if (Yii::$app->urlManager::className() == UrlManager::class || (isset(Yii::$app->urlManager->languages[0]) && Yii::$app->urlManager->languages[0] == $this->lang))
            return urldecode(Url::toRoute(['/pages/page/view', 'path' => $this->path]));
        else
            return urldecode(Url::toRoute(['/pages/page/view', 'path' => $this->path, 'language' => $this->lang]));
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateUrlLog();
        parent::afterSave($insert, $changedAttributes);
    }

    private function updateUrlLog()
    {
        PageUrl::deleteAll(['page_id' => $this->id, 'url' => $this->path]);
        $pageUrl = new PageUrl(['page_id' => $this->id, 'url' => $this->path, 'created_at' => time()]);
        if (!$pageUrl->save()) {
            throw new ErrorException('Unable to save page url entity: ' . print_r($pageUrl->errors, true));
        }
    }

    /**
     * @return PageQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * @return PageQuery
     */
    public function getChildVisible()
    {
        return $this->getChild()->andWhere(['menu' => PageMenuVisibility::VISIBLE]);
    }

    /**
     * @return PageQuery
     */
    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])
            ->orderBy('norder');
    }

    /**
     * @return array
     */
    public function getChild_ids()
    {
        return $this->getChild()->select('id')->column();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Yii::$app->getModule('pages')->userModel, ['id' => 'create_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdator()
    {
        return $this->hasOne(Yii::$app->getModule('pages')->userModel, ['id' => 'update_user_id']);
    }

    public function isLink()
    {
        return strlen($this->link) > 0;
    }

    /**
     * @return PageParam[]
     */
    public function getPageParams()
    {
        try {
            list($controller, $action) = explode('::', $this->index_action);
        } catch (\Exception $e) {
            return [];
        }
        $actionParts = explode('-', $action);
        foreach ($actionParts as $key => $part) {
            $actionParts[$key] = ucfirst($part);
        }
        $finalAction = 'action' . ucfirst(implode($actionParts));
        $pageParams = Annotations::read($controller, $finalAction);
        foreach ($pageParams as $key => $param) {
            if ($param->name == 'page') {
                unset($pageParams[$key]);
                continue;
            }
            foreach ((array)$this->page_params as $key => $value) {
                if ($param->name == $key) {
                    $param->value = $value;
                }
            }
        }
        return $pageParams;
    }

    public function __toString()
    {
        return $this->title_menu;
    }
}
