<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 14:11
 *
 * @var $this \yii\web\View
 * @var $model \floor12\pages\models\Page
 */

use yii\helpers\Html;

echo $model->title ? Html::tag('h1', $model->title) : null;

echo $model->content;

if ($model->images)
    echo $this->render('images', ['models' => $model->images]);

if ($model->files)
    echo \floor12\files\components\FileListWidget::widget([
        'files' => $model->files,
    ]);




