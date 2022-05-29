<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 24.10.2016
 * Time: 20:22
 *
 * @var \floor12\pages\models\Page $model
 *
 */

use floor12\editmodal\ModalWindow;
use floor12\files\components\FileInputWidget;
use floor12\pages\models\Page;
use floor12\summernote\Summernote;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'page-form',
    'options' => ['class' => 'modaledit-form'],
    'enableClientValidation' => false
]);

if (Yii::$app->request->get('parent_id'))
    $model->parent_id = intval(Yii::$app->request->get('parent_id'));

?>
<div class="modal-header">
    <div class="pull-right">
        <?= ModalWindow::btnFullscreen() ?>
        <?= ModalWindow::btnClose() ?>
    </div>
    <h2><?= $model->isNewRecord ? "Добавление страницы" : "Редактирование страницы"; ?></h2>
</div>
<div class="modal-body">

    <?= $form->errorSummary($model); ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#page-main" aria-controls="home" role="tab" data-toggle="tab">Основное</a>
        </li>
        <li role="presentation">
            <a href="#page-params" aria-controls="profile" role="tab" data-toggle="tab">Дополнительно</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="page-main">
            <br>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'title_menu')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'title_seo')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'description_seo')->textarea(['style' => 'height: 108px;']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'key')->textInput() ?>
                    <?= $form->field($model, 'link')->label('...или внешняя ссылка')->textInput() ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'status')->checkbox() ?>
                    <?= $form->field($model, 'menu')->checkbox() ?>
                    <?= $form->field($model, 'use_purifier')->checkbox() ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="page-params">
            <br>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'index_controller')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'index_action')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'index_params')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'view_controller')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'view_action')->textInput() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'lang')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'layout') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'parent_id')->dropDownList(Page::find()->select('title')->indexBy('id')->orderBy("parent_id, norder")->column(), ['prompt' => ['options' => ['value' => '0'], 'text' => 'Корень']]) ?>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'content')->widget(Summernote::className(), []) ?>


        <div class="row">

            <div class="col-md-9">
                <?= $form->field($model, 'files')->widget(FileInputWidget::className(), []) ?>
                <?= $form->field($model, 'images')->widget(FileInputWidget::className(), []) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'banner')->widget(FileInputWidget::className(), []) ?>
            </div>
        </div>


    </div>

    <div class=" modal-footer
    ">
        <?= Html::button('Отмена', ['class' => 'btn btn-default modaledit-disable']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
