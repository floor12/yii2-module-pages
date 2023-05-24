<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.11.2016
 * Time: 18:11
 *
 * @var floor12\pages\models\Page $model
 * @var floor12\pages\models\Page $currentPage
 *
 */

use yii\helpers\Html;

?>


<li class="<?= $currentPage && $model->id == $currentPage->id ? "active" : NULL ?>">
    <?= Html::a($model->title_menu, $model->url, ['data-pjax' => '0']) ?>

    <?php if ($model->child): ?>
        <ul class="dropDownSubMenu">
            <?php foreach ($model->child as $child) if ($child->menu) { ?>
                <li class="<?= $currentPage && $child->id == $currentPage->id ? "active" : NULL ?>">
                    <a href="<?= $child->url ?>" data-pjax="0"><?= $child->title_menu ?></a>

                    <?php if ($child->childVisible): ?>
                        <ul class="dropDownSubMenu">
                            <?php foreach ($child->child as $subchild) if ($subchild->menu) { ?>
                                <li class="<?= $currentPage && $subchild->id == $currentPage->id ? "active" : NULL ?>">
                                    <a href="<?= $subchild->url ?>" data-pjax="0"><?= $subchild->title_menu ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php endif; ?>

                </li>
            <?php } ?>
        </ul>
    <?php endif; ?>
</li>


