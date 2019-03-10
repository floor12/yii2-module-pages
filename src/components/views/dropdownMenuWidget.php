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
use yii\helpers\Html;

?>

<?php if ($model->menu == PageMenuVisibility::VISIBLE): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">

        <?= Html::a($model->title_menu, $model->url, ['data-pjax' => '0']) ?>

        <?php if ($model->childVisible): ?>
            <ul class="dropDownSubMenu">
                <?php foreach ($model->childVisible as $child) { ?>
                    <li>
                        <a href="<?= $child->url ?>" data-pjax="0"><?= $child->title_menu ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php endif; ?>

    </li>
<?php endif; ?>

