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

use floor12\editmodal\EditModalHelper;
use floor12\pages\assets\IconHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

if (Yii::$app->getModule('pages')->adminMode()) {
    echo Html::a(IconHelper::PENCIL, null, [
        'class' => 'btn btn-default btn-xs pull-right',
        'onclick' => EditModalHelper::showForm(['page/form'], $model->id)]);
    Pjax::begin(['id' => 'items']);
}

echo Html::tag('h1', $model->title);

echo $model->content;

if (Yii::$app->getModule('pages')->adminMode())
    Pjax::end();



