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


<?php if ($adminMode): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">

        <a title="<?= $model->title_seo ?>" href="<?= $model->url ?>" data-pjax="0">
            <?= ($model->menu) ? $model->title_menu : Html::tag('strike', $model->title_menu); ?>
        </a>

    </li>
<?php elseif ($model->menu): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">
        <?= Html::a($model->title_menu, $model->url, ["data-pjax" => "0"]) ?>
    </li>
<?php endif; ?>

