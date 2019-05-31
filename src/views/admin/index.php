<?php
/**
 * @var $this \yii\web\View
 * @var $model PageFilter
 */

use common\src\enum\StatusEnum;
use floor12\editmodal\EditModalHelper;
use floor12\pages\assets\IconHelper;
use floor12\pages\assets\PagesAsset;
use floor12\pages\models\Page;
use floor12\pages\models\PageFilter;
use floor12\pages\models\PageStatus;
use leandrogehlen\treegrid\TreeGrid;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

PagesAsset::register($this);

$this->title = "Страницы";

$columns = [
    [
        'attribute' => 'title',
        'content' => function (Page $model) {
            if (!$model->parent_id)
                return Html::tag('b', $model->title);
            return $model->title;
        }
    ],
    [
        'contentOptions' => ['style' => 'text-align:right'],
        'class' => \floor12\editmodal\EditModalColumn::class,
    ]
];

echo Html::a(IconHelper::PLUS . "  Добавить страницу", null, [
        'onclick' => EditModalHelper::showForm(['/pages/page/form'], 0),
        'class' => 'btn btn-primary btn-sm pull-right'
    ]) . " ";

echo Html::tag('h1', $this->title);


?>


<?php

$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'method' => "GET",
    'options' => [
        'class' => 'autosubmit',
        'data-container' => '#items'
    ]]) ?>

    <div class="filter-block">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'filter')->label(false)->textInput(['placeholder' => 'Поиск...']) ?>
            </div>
        </div>

    </div>

<?php ActiveForm::end();


Pjax::begin(['id' => 'items',
    'scrollTo' => true,]);

if ($model->filter)
    echo \yii\grid\GridView::widget([
        'dataProvider' => $model->dataProvider(),
        'tableOptions' => ['class' => 'table'],
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => $columns
    ]);

else
    echo TreeGrid::widget(['dataProvider' => $model->dataProvider(),
        'options' => ['class' => 'table'],
        'rowOptions' => function (Page $model) {
            $class = '';
            $color = PageFilter::makeColor($model);
            if ($model->status == PageStatus::DISABLED)
                $class = 'disabled';

            return ['class' => $class, 'style' => "background-color: {$color}"];
        },
        'keyColumnName' => 'id',
        'parentColumnName' => 'parent_id',
        'pluginOptions' => [
            'initialState' => 'collapsed',
            'saveState' => true],
        'columns' => $columns
    ]);

Pjax::end(); 

