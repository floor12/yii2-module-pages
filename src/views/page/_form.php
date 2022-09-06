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
use floor12\textcounter\TextCounterWidget;
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
    <h2><?= Yii::t('app.f12.pages', $model->isNewRecord ? "Create new page" : "Update page"); ?></h2>
</div>
<div class="modal-body">

    <?= $form->errorSummary($model); ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#page-main" aria-controls="home" role="tab" data-toggle="tab">
                <?= Yii::t('app.f12.pages', 'Main') ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#page-params" aria-controls="profile" role="tab" data-toggle="tab">
                <?= Yii::t('app.f12.pages', 'Params') ?>
            </a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="page-main">
            <br>
            <div class="row">

                <div class="col-md-7">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-title',
                        'min' => 10,
                        'max' => 70
                    ]) ?>
                </div>

                <div class="col-md-5">
                    <?= $form->field($model, 'title_menu')->textInput(['maxlength' => true]) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-title_menu',
                        'min' => 5,
                        'max' => 30
                    ]) ?>
                </div>

                <div class="col-md-12">
                    <?= $form->field($model, 'title_seo')->textInput(['maxlength' => true]) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-title_seo',
                        'min' => 10,
                        'max' => 60
                    ]) ?>
                </div>
            </div>
            <div class="row">




                <div class="col-md-6">
                    <?= $form->field($model, 'description_seo')->textarea(['style' => 'height: 108px;']) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-description_seo',
                        'min' => 50,
                        'max' => 160
                    ]) ?>
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
                    <?= $form->field($model, 'parent_id')->dropDownList(Page::find()->select('title')->indexBy('id')->orderBy("parent_id, norder")->column(), ['prompt' => ['options' => ['value' => '0'], 'text' => Yii::t('app.f12.pages', 'root')]]) ?>
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
        <?= Html::button(Yii::t('app.f12.pages', 'Cancel'), ['class' => 'btn btn-default modaledit-disable']) ?>
        <?= Html::submitButton(Yii::t('app.f12.pages', $model->isNewRecord ? 'Create' : 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
