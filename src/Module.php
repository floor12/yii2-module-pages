<?php

namespace floor12\pages;

/**
 * pages module definition class
 * @property  string $editRole
 */
class Module extends \yii\base\Module
{

    public $currentPageId = 0;

    public $layout = '@app/views/layouts/main';

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
        parent::init();

    }

    public function adminMode()
    {
        if ($this->editRole == '@')
            return !\Yii::$app->user->isGuest;
        else
            return \Yii::$app->user->can($this->editRole);
    }
}
