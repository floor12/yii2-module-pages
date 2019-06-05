<?php

namespace floor12\pages;

use Yii;

/**
 * pages module definition class
 * @property  string $editRole
 */
class Module extends \yii\base\Module
{

    public $currentPageId = 0;

    public $layout = '@app/views/layouts/main';

    public $layoutAdmin = '@app/views/layouts/main';

    public $view = 'view';

    public $userModel = 'app\models\User';

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
}
