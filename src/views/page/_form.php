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
use floor12\pages\widgets\PageParamInputWidget;
use floor12\summernote\Summernote;
use floor12\textcounter\TextCounterWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJs("summernoteParams.height = 550");

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


    <div style="display: flex; float: right; justify-content: space-between; width: 280px; margin: 7px 0 -18px 0;">
        <?= $form->field($model, 'menu')->checkbox() ?>
        <?= $form->field($model, 'status')->checkbox() ?>
    </div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#page-main" aria-controls="main" role="tab" data-toggle="tab">
                <?= Yii::t('app.f12.pages', 'Main') ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#page-params" aria-controls="params" role="tab" data-toggle="tab">
                <?= Yii::t('app.f12.pages', 'Extra options') ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#page-files" aria-controls="files" role="tab" data-toggle="tab">
                <?= Yii::t('app.f12.pages', 'Images and files') ?>
            </a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="page-main">
            <br>
            <div class="row">
                <div class="col-md-7">
                    <div style="position: relative">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                        <?= TextCounterWidget::widget([
                            'targetId' => 'page-title',
                            'min' => 10,
                            'max' => 70
                        ]) ?>
                    </div>
                    <div style="position: relative">
                        <?= $form->field($model, 'title_seo')->textInput(['maxlength' => true]) ?>
                        <?= TextCounterWidget::widget([
                            'targetId' => 'page-title_seo',
                            'min' => 10,
                            'max' => 60
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-5">
                    <?= $form->field($model, 'description_seo')->textarea(['style' => 'height: 132px;']) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-description_seo',
                        'min' => 50,
                        'max' => 160
                    ]) ?>

                </div>


            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'title_menu')->textInput(['maxlength' => true]) ?>
                    <?= TextCounterWidget::widget([
                        'targetId' => 'page-title_menu',
                        'min' => 5,
                        'max' => 30
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'key')->textInput() ?>

                </div>
                <div class="col-md-5">
                    <?= $form->field($model, 'link')->label(Yii::t('app.f12.pages', '...or external URL'))->textInput() ?>


                </div>
            </div>

            <div style="float: right; margin: 28px 0 -10px 0;">
                <?= $form->field($model, 'use_purifier')->checkbox() ?>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane" id="page-params">
            <br>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'index_action')
                        ->dropDownList(Yii::$app->getModule('pages')->actionsIndex, ['prompt' => 'Простая страница']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'view_action')
                        ->dropDownList(Yii::$app->getModule('pages')->actionsView, ['prompt' => 'нет']) ?>
                </div>
            </div>

            <?php if ($params = $model->getPageParams()): ?>
                <br>
                <br>
                <div class="row">
                    <?php foreach ($params as $key => $pageParam): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= PageParamInputWidget::widget(['model' => $pageParam]); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <br>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'layout') ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($model, 'parent_id')->dropDownList(Page::find()->select('title')->indexBy('id')->orderBy("parent_id, norder")->column(), ['prompt' => ['options' => ['value' => '0'], 'text' => Yii::t('app.f12.pages', 'root')]]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'lang')->textInput([
                        'value' => $model->lang ?: Yii::$app->language
                    ]) ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="page-files">
            <br>
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
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#form-content" aria-controls="home" role="tab" data-toggle="tab">
                    <?= Yii::t('app.f12.pages', 'Content') ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#form-announce" aria-controls="profile" role="tab" data-toggle="tab">
                    <?= Yii::t('app.f12.pages', 'Announce') ?>
                </a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="form-content">
                <?= $form->field($model, 'content')
                    ->label(false)
                    ->widget(Summernote::className(), []) ?>
            </div>
            <div role="tabpanel" class="tab-pane " id="form-announce">
                <?= $form->field($model, 'announce')
                    ->label(false)
                    ->widget(Summernote::className(), []) ?>
            </div>
        </div>


    </div>

    <div class=" modal-footer
    ">
        <?= Html::button(Yii::t('app.f12.pages', 'Cancel'), ['class' => 'btn btn-default modaledit-disable']) ?>
        <?= Html::submitButton(Yii::t('app.f12.pages', $model->isNewRecord ? 'Create' : 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
