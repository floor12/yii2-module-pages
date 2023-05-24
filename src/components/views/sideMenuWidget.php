<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.11.2016
 * Time: 18:11
 *
 * @var floor12\pages\models\Page $model
 * @var boolean $adminMode
 *
 */

use yii\helpers\Html;

?>


<li class="<?php if ($model->active) echo "active"; ?>">
    <?= Html::a($model->title_menu, $model->url, ["data-pjax" => "0"]) ?>
</li>
