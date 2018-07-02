<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.11.2016
 * Time: 18:11
 *
 * @var floor12\pages\Page $model
 * @var boolean $adminMode
 *
 */

use yii\helpers\Html;
use common\components\FontAwesome;
use floor12\editmodal\ModalWindow;
use floor12\editmodal\EditModalHelper;

?>


<?php if ($adminMode): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">
        <a title="<?= $model->title_seo ?>" href="<?= $model->url ?>" data-pjax="0">
            <?= ($model->menu) ? $model->title_menu : Html::tag('strike', $model->title_menu); ?>
        </a>


        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <?= Html::a(FontAwesome::icon('pencil'), null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], $model->id, '#dropdownMenuControl'), 'title' => 'Редактировать']) ?>
                    <?= Html::a(FontAwesome::icon('chevron-up'), null, ['onclick' => "pageMove({$model->id},0,'#dropdownMenuControl')", 'title' => 'Поднять']) ?>
                    <?= Html::a(FontAwesome::icon('chevron-down'), null, ['onclick' => "pageMove({$model->id},1,'#dropdownMenuControl')", 'title' => 'Опустить']) ?>
                    <?= Html::a(FontAwesome::icon('plus'), null, ['onclick' => EditModalHelper::showForm(['page/form'], ['id' => 0, 'parent_id' => $model->id]), 'title' => 'Создать подраздел']) ?>
                    <?= Html::a(FontAwesome::icon('trash'), null, ['onclick' => EditModalHelper::deleteItem('/pages/page/delete', $model->id, '#dropdownMenuControl'), 'title' => 'Удалить']) ?>
                </li>

            </ul>
        </div>

        <?php if ($model->child): ?>
            <ul class="dropDownSubMenu">
                <?php foreach ($model->child as $child) { ?>
                    <li>
                        <a href="<?= $child->url ?>" data-pjax="0"
                           class="<?= !$child->menu ? "strike" : NULL ?>"><?= $child->title_menu ?></a>

                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= Html::a(FontAwesome::icon('pencil'), null, ['onclick' => EditModalHelper::showForm(['/pages/page/form'], $child->id), 'title' => 'Редактировать']) ?>
                                    <?= Html::a(FontAwesome::icon('chevron-up'), null, ['onclick' => "pageMove({$child->id},0,'#dropdownMenuControl')", 'title' => 'Поднять']) ?>
                                    <?= Html::a(FontAwesome::icon('chevron-down'), null, ['onclick' => "pageMove({$child->id},1,'#dropdownMenuControl')", 'title' => 'Опустить']) ?>
                                    <?= Html::a(FontAwesome::icon('plus'), null, ['onclick' => EditModalHelper::showForm(['page/form'], ['id' => 0, 'parent_id' => $child->id]), 'title' => 'Создать подраздел']) ?>
                                    <?= Html::a(FontAwesome::icon('trash'), null, ['onclick' => EditModalHelper::deleteItem('/pages/page/delete', $child->id, '#dropdownMenuControl'), 'title' => 'Удалить']) ?>
                                </li>

                            </ul>
                        </div>

                    </li>
                <?php } ?>
            </ul>
        <?php endif; ?>
    </li>
<?php elseif ($model->menu): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">
        <?= Html::a($model->title_menu, $model->url, ['data-pjax' => '0']) ?>
        <?php if ($model->child): ?>
            <ul class="dropDownSubMenu">
                <?php foreach ($model->child as $child) if ($child->menu) { ?>
                    <li>
                        <a href="<?= $child->url ?>" data-pjax="0"><?= $child->title_menu ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endif; ?>

