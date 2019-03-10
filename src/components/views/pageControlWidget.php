<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 2019-03-10
 * Time: 11:01
 *
 * @var $this \yii\web\View
 * @var $model \floor12\pages\models\Page
 */

use floor12\editmodal\EditModalHelper;
use floor12\pages\assets\IconHelper;
use yii\helpers\Html;

?>

<div class="f12-page-control-dropdown">
    <button><?= IconHelper::DROPDOWN ?></button>
    <div>
        <?= Html::a(IconHelper::PENCIL, null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], $model->id), 'title' => 'Редактировать']) ?>
        <?= Html::a(IconHelper::ARROW_UP, null, ['onclick' => "f12pages.move({$model->id},0,'#dropdownMenuControl')", 'title' => 'Поднять']) ?>
        <?= Html::a(IconHelper::ARROW_DOWN, null, ['onclick' => "f12pages.move({$model->id},1,'#dropdownMenuControl')", 'title' => 'Опустить']) ?>
        <?= Html::a(IconHelper::PLUS, null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], ['id' => 0, 'parent_id' => $model->id])], ['title' => 'Создать подраздел']) ?>
        <?= Html::a(IconHelper::TRASH, null, ['onclick' => EditModalHelper::deleteItem('/pages/page/delete', $model->id), 'title' => 'Удалить']) ?>
    </div>
</div>

