<?php

namespace floor12\pages\components;

use yii\web\IdentityInterface;

/**
 * Лёгкая заглушка IdentityInterface для запросов, авторизованных через REST API токеном
 * (controllers/ApiController), а не через реального пользователя приложения-хоста.
 * Нужна только чтобы переиспользовать logic/PageUpdate и floor12\files\logic\FileCreateFromInstance,
 * конструкторы которых типизированы на IdentityInterface.
 */
class ApiIdentity implements IdentityInterface
{
    public function __construct(private $id = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentity($id)
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}
