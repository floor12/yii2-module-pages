<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 14:11
 *
 * @var $this \yii\web\View
 * @var $model \floor12\pages\Page
 */

use floor12\editmodal\EditModalHelper;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\helpers\Html;
use yii\widgets\Pjax;


if (Yii::$app->getModule('pages')->adminMode()):
    echo Html::a(FontAwesome::icon('pencil'), null, ['class' => 'btn btn-default btn-xs pull-right', 'onclick' => EditModalHelper::showForm(['page/form'], $model->id)]);
    Pjax::begin(['id' => 'items']);
endif;

echo Html::tag('h1', $model->title);

echo $model->content;


if (Yii::$app->getModule('pages')->adminMode()):
    Pjax::end();
endif;


