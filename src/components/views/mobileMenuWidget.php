<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.11.2016
 * Time: 18:11
 *
 * @var floor12\pages\Page $model
 * @var floor12\pages\Page $currentPage
 *
 */

use yii\helpers\Html;

?>


<li class="<?php if ($model->active) echo "active"; ?>">
    <?= Html::a($model->title_menu, $model->url) ?>

    <?php if ($model->child): ?>
        <ul class="dropDownSubMenu">
            <?php foreach ($model->child as $child) if ($child->menu) { ?>
                <li class="<?= $currentPage && $child->id == $currentPage->id ? "active" : NULL ?>">
                    <a href="<?= $child->url ?>" data-pjax="0"><?= $child->title_menu ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php endif; ?>
</li>


