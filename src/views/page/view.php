<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 10.04.2018
 * Time: 14:11
 *
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */

use yii\widgets\Pjax; 
use Yii;
use yii\helpers\Html;
use common\components\FontAwesome;
use floor12\editmodal\ModalWindow;
use \floor12\editmodal\EditModalHelper;

if (Yii::$app->getModule('pages')->adminMode()):
    echo Html::a(FontAwesome::icon('pencil'), null, ['class' => 'btn btn-default btn-xs pull-right', 'onclick' => EditModalHelper::showForm(['page/form'], $model->id)]);
endif;

?>

<?php Pjax::begin(['id' => 'items']) ?>
<div class="h1-wrapper h1-wrapper-margin">
    <h1><?= $model->title ?></h1>
    <div></div>
</div>

<?= $model->content ?>

<?php Pjax::end(); ?>

