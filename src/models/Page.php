<?php

namespace floor12\pages\models;

use floor12\files\components\FileBehaviour;
use floor12\files\models\File;
use Yii;
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
 * @property boolean $active Активна ли данная страницы
 * @property string $index_controller Контроллер индекса
 * @property string $index_action Экшн индекса
 * @property string $index_params Параметры экшена индекса
 * @property string $view_controller Контроллер просмотра объекта
 * @property string $view_action Экшн для просмотра объекта
 * @property string $lang Язык страницы
 *
 * @property User $creator
 * @property User $updator
 * @property Page $parent
 * @property Page[] $child
 * @property Page[] $childVisible
 * @property array $child_ids
 * @property File[] $images
 */
class Page extends ActiveRecord
{
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
            [['created', 'updated', 'title_seo', 'key', 'title_menu'], 'required'],
            [['content'], 'string'],
            ['lang', 'string', 'max' => 3],
            [['title', 'title_seo', 'title_menu', 'path', 'index_params', 'view_action', 'view_controller', 'index_action', 'index_controller'], 'string', 'max' => 255],
            [['description_seo', 'keywords_seo', 'key'], 'string', 'max' => 400],
            [['layout'], 'string', 'max' => 255],
            ['status', 'in', 'range' => [PageStatus::ACTIVE, PageStatus::DISABLED]],
            ['menu', 'in', 'range' => [PageMenuVisibility::VISIBLE, PageMenuVisibility::HIDDEN]],
            [['create_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->getModule('pages')->userModel, 'targetAttribute' => ['create_user_id' => 'id']],
            [['update_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->getModule('pages')->userModel, 'targetAttribute' => ['update_user_id' => 'id']],
            ['key', 'match', 'pattern' => '/^[-a-z0-9\/]*$/', 'message' => 'Ключ URL может состоять только из латинских букв в нижнем регистре, цифр и дефиса.'],
            ['images', 'file', 'maxFiles' => 10, 'extensions' => ['jpeg', 'png', 'jpg', 'svg']],
            ['files', 'file', 'maxFiles' => 10]
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'files' => [
                'class' => FileBehaviour::class,
                'attributes' => [
                    'images' => [
                        'maxWidth' => 3800,
                        'maxHeight' => 3800,
                    ],
                    'files'
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
            'status' => 'Скрыть',
            'created' => 'Время создания',
            'updated' => 'Время обновления',
            'create_user_id' => 'Создал',
            'update_user_id' => 'Обновил',
            'parent_id' => 'Родительский раздел',
            'title' => 'Заголовок страницы',
            'title_seo' => 'Title страницы',
            'title_menu' => 'Название меню',
            'description_seo' => 'Meta Description',
            'keywords_seo' => 'Meta keywords',
            'key' => 'Ключевое слово для URL',
            'norder' => 'Порядок',
            'path' => 'Полный путь',
            'content' => 'Тело страницы',
            'layout' => 'Layout',
            'menu' => 'Показывать в меню',
            'view_action' => 'View Action',
            'view_controller' => 'View Controller',
            'index_action' => 'Index Action',
            'index_params' => 'Index Params',
            'index_controller' => 'Index Controller',
            'lang' => 'Язык страницы',
            'files' => 'Файлы',
            'images' => 'Галерея',
        ];
    }

    /**
     * @return string Full uri of current page.
     */
    public function getUrl()
    {
        if ($this->path == '/') {
            if (Yii::$app->urlManager::className() == UrlManager::class)
                return '/';
            if ($this->lang == 'ru')
                return '/';

            return '/' . $this->lang;
        }

        if (!strip_tags($this->content) && $this->child && !$this->index_controller)
            return $this->child[0]->url;

        if (Yii::$app->urlManager::className() == UrlManager::class || $this->lang == 'ru')
            return urldecode(Url::toRoute(['/pages/page/view', 'path' => $this->path]));
        else
            return urldecode(Url::toRoute(['/pages/page/view', 'path' => $this->path, 'language' => $this->lang]));
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
}
