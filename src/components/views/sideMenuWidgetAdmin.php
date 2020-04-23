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

use floor12\pages\components\PageControlWidget;
use yii\helpers\Html;

?>


<li class="<?php if ($model->active) echo "active"; ?>">
    <?= PageControlWidget::widget(['model' => $model]) ?>
    <a title="<?= $model->title_seo ?>" href="<?= $model->url ?>" data-pjax="0">
        <?= ($model->menu) ? $model->title_menu : Html::tag('strike', $model->title_menu); ?>
    </a>
</li>
