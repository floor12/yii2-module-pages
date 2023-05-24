<?php
/**
 * @var $this View
 * @var $model PageFilter
 */

use common\src\enum\StatusEnum;
use floor12\editmodal\EditModalHelper;
use floor12\pages\assets\IconHelper;
use floor12\pages\assets\PagesAsset;
use floor12\pages\models\Page;
use floor12\pages\models\PageFilter;
use leandrogehlen\treegrid\TreeGrid;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

PagesAsset::register($this);

$this->title = Yii::t('app.f12.pages', 'Pages');

$columns = [
    [
        'attribute' => 'title',
        'content' => function (Page $model) {
            $html = Html::tag('span', $model->id, ['class' => 'page-id']);
            $html .= Html::tag('span', $model->title_menu, ['class' => 'page-menu-title']);
            if ($model->isLink())
                $html .= Html::tag('div', '→ ' . $model->link, ['class' => 'page-menu-url small', 'target' => '_blank', 'data-pjax' => '0']);
            else
                $html .= Html::a($model->url, $model->url, ['class' => 'page-menu-url small', 'target' => '_blank', 'data-pjax' => '0']);
            return Html::tag('div', $html, []);
        }
    ],
    [
        'header' => Yii::t('app.f12.pages', 'Content'),
        'content' => function (Page $model) {
            $html = '';

            if ($model->content)
                $html .= Html::tag('div', StringHelper::truncateWords(strip_tags($model->content), 10), ['class' => 'small']);

            if ($model->index_controller)
                $html .= Html::tag('div', "<b>{$model->index_controller}</b>::$model->index_action", ['class' => 'small']);

            if ($model->view_controller)
                $html .= Html::tag('div', "<b>{$model->view_controller}</b>::$model->view_action", ['class' => 'small']);


            return $html;
        }
    ],
    [
        'contentOptions' => ['style' => 'text-align:right; min-width: 210px;'],
        'content' => function (Page $model) {
            $html = '';

            $html .= Html::button(IconHelper::PLUS, [
                    'onclick' => EditModalHelper::showForm(['/pages/admin/form'], ['id' => 0, 'parent_id' => $model->id]),
                    'title' => Yii::t('app.f12.pages', 'Create subpage'),
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= Html::button(IconHelper::ARROW_UP, [
                    'onclick' => "f12pages.move({$model->id},0,'#pages')",
                    'title' => Yii::t('app.f12.pages', 'Move up'),
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= Html::button(IconHelper::ARROW_DOWN, [
                    'onclick' => "f12pages.move({$model->id},1,'#pages')",
                    'title' => Yii::t('app.f12.pages', 'Move down'),
                    'class' => 'btn btn-default btn-sm'
                ]) . ' ';

            $html .= EditModalHelper::editBtn('form', $model->id) . ' ';

            $html .= EditModalHelper::deleteBtn('delete', $model->id);
            return $html;
        }
    ]
];

echo Html::button(IconHelper::PLUS . ' ' . Yii::t('app.f12.pages', 'Create page'), [
        'onclick' => EditModalHelper::showForm(['/pages/admin/form'], 0),
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
                    ->textInput(['placeholder' => Yii::t('app.f12.pages', 'Search')])
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
    echo GridView::widget([
        'dataProvider' => $model->dataProvider(),
        'tableOptions' => ['class' => 'table table-striped'],
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => $columns
    ]);

else
    echo TreeGrid::widget(['dataProvider' => $model->dataProvider(),
        'options' => ['class' => 'table table-striped'],
        'rowOptions' => function (Page $model) {
            if ($model->isLink() && $model->status == \floor12\pages\models\PageStatus::ACTIVE) {
                $class = 'page-blue';
            } elseif ($model->menu == \floor12\pages\models\PageMenuVisibility::VISIBLE) {
                $class = 'page-green';
            } elseif ($model->status == \floor12\pages\models\PageStatus::ACTIVE) {
                $class = 'page-yellow';
            } else {
                $class = 'page-disabled';
            }
            return ['class' => $class];
        },
        'keyColumnName' => 'id',
        'parentColumnName' => 'parent_id',
        'pluginOptions' => [
            'initialState' => 'collapsed',
            'saveState' => true],
        'columns' => $columns
    ]);

Pjax::end(); 

