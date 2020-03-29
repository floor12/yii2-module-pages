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
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

PagesAsset::register($this);

$this->title = "Страницы";

$columns = [
    [
        'attribute' => 'title',
        'content' => function (Page $model) {

            $html = Html::tag('span', $model->title_menu, ['class' => $model->parent_id ? '' : 'bold']);

            $html .= Html::a($model->url, $model->url, ['class' => 'small', 'target' => '_blank', 'data-pjax' => '0']);

            return Html::tag('div', $html, []);
        }
    ],
    [
        'header' => 'Содрежание',
        'contentOptions' => ['style' => 'width: 50%;'],
        'content' => function (Page $model) {
            if ($model->index_controller)
                $html = Html::tag('div', "<b>{$model->index_controller}</b>::$model->index_action", ['class' => 'small']);

            if ($model->view_controller)
                $html .= Html::tag('div', "<b>{$model->view_controller}</b>::$model->view_action", ['class' => 'small']);

            if (empty($html))
                $html = Html::tag('div', StringHelper::truncateWords(strip_tags($model->content), 10), ['class' => 'small']);

            return $html;
        }
    ],
    [
        'contentOptions' => ['style' => 'text-align:right; min-width: 210px;'],
        'content' => function (Page $model) {
            $html = '';

            $html .= Html::button(IconHelper::PLUS, [
                    'onclick' => EditModalHelper::showForm(['/pages/page/form'], ['id' => 0, 'parent_id' => $model->id]),
                    'title' => 'Создать подраздел',
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= Html::button(IconHelper::ARROW_UP, [
                    'onclick' => "f12pages.move({$model->id},0,'#pages')",
                    'title' => 'Сдвинуть вверх',
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= Html::button(IconHelper::ARROW_DOWN, [
                    'onclick' => "f12pages.move({$model->id},1,'#pages')",
                    'title' => 'Сдвинуть вниз',
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= EditModalHelper::editBtn('form', $model->id) . ' ';

            $html .= EditModalHelper::deleteBtn('delete', $model->id);
            return $html;
        }
    ]
];

echo Html::button(IconHelper::PLUS . "  Добавить страницу", [
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
        'data-container' => '#pages'
    ]]) ?>

    <div class="filter-block">
        <div class="row">
            <div class="col-md-10">
                <?= $form->field($model, 'filter')
                    ->label(false)
                    ->textInput(['placeholder' => 'Поиск...'])
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'lang')
                    ->label(false)
                    ->dropDownList($model->getLangs())
                ?>
            </div>
        </div>

    </div>

<?php ActiveForm::end();


Pjax::begin(['id' => 'pages',
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
            $class = $model->index_controller ? "page-controller" : NULL;
            $color = PageFilter::makeColor($model);
            if ($model->status == PageStatus::DISABLED)
                $class .= ' disabled';

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

