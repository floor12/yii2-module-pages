<?php

namespace floor12\pages;

use floor12\pages\models\Page;
use Yii;


class Module extends \yii\base\Module
{

    public $currentPageId = 0;

    public $layout = '@app/views/layouts/main';

    public $layoutAdmin = '@app/views/layouts/main';

    public $viewForm = '@vendor/floor12/yii2-module-pages/src/views/page/_form';

    public $view = '@vendor/floor12/yii2-module-pages/src/views/page/view';

    public $userModel = 'app\models\User';

    public $enableDragNDropSort = false;

    public $pageModel = Page::class;

    public $actionsIndex = [];

    public $actionsView = [];
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'floor12\pages\controllers';

    /**
     * Те роли в системе, которым разрешено редактирование страниц
     * @var array
     */
    public $editRole = '@';

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::$container->set('leandrogehlen\treegrid\TreeGridAsset', [
            'js' => [
                'js/jquery.cookie.js',
                'js/jquery.treegrid.min.js',
            ]
        ]);

        $this->registerTranslations();
        parent::init();
    }

    /**
     * @return bool
     */
    public function adminMode()
    {
        if ($this->editRole == '@')
            return !\Yii::$app->user->isGuest;
        else
            return \Yii::$app->user->can($this->editRole);
    }

    /**
     * Register some lang files
     * @return void
     */

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app.f12.pages'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'fileMap' => [
                'app.f12.pages' => 'pages.php',
            ],
            'basePath' => '@vendor/floor12/yii2-module-pages/src/messages',
            'sourceLanguage' => 'en-US',
        ];
    }
}
