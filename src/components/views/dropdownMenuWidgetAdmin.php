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

use floor12\pages\models\PageMenuVisibility;
use floor12\pages\components\PageControlWidget;
use yii\helpers\Html;


?>

<li class="<?php if ($model->active) echo "active"; ?>">

    <?php
    if ($model->menu == PageMenuVisibility::VISIBLE)
        echo Html::a($model->title_menu, $model->url, ['data-pjax' => '0']);
    else
        echo Html::a("<strike>$model->title_menu</strike>", $model->url, ['data-pjax' => '0']);
    ?>

    <?= PageControlWidget::widget(['model' => $model]) ?>

    <?php if ($model->child): ?>
        <ul class="dropDownSubMenu">
            <?php foreach ($model->child as $child) { ?>
                <li>
                    <?php
                    if ($child->menu == PageMenuVisibility::VISIBLE)
                        echo Html::a($child->title_menu, $child->url, ['data-pjax' => '0']);
                    else
                        echo Html::a("<strike>$child->title_menu</strike>", $child->url, ['data-pjax' => '0']);
                    ?>

                    <?= PageControlWidget::widget(['model' => $child]) ?>
                </li>
            <?php } ?>
        </ul>
    <?php endif; ?>

</li>
