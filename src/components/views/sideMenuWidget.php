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

?>


<?php if ($adminMode): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">

        <div class="pull-right">

            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <?= Html::a(FontAwesome::icon('pencil'), null, ['onclick' => ModalWindow::showForm(['/pages/page/form'], $model->id), 'title' => 'Редактировать']) ?>
                        <?= Html::a(FontAwesome::icon('chevron-up'), null, ['onclick' => "pageMove({$model->id},0,'#menuControl')", 'title' => 'Поднять']) ?>
                        <?= Html::a(FontAwesome::icon('chevron-down'), null, ['onclick' => "pageMove({$model->id},1,'#menuControl')", 'title' => 'Опустить']) ?>
                        <?= Html::a(FontAwesome::icon('plus'), null, ['onclick' => ModalWindow::showForm(['page/form'], ['id' => 0, 'parent_id' => $model->id]), 'title' => 'Создать подраздел']) ?>
                        <?= Html::a(FontAwesome::icon('trash'), null, ['onclick' => ModalWindow::deleteItem('/pages/page/delete', $model->id, '#menuControl'), 'title' => 'Удалить']) ?>
                    </li>

                </ul>
            </div>

        </div>


        <a title="<?= $model->title_seo ?>" href="<?= $model->url ?>" data-pjax="0">
            <?= ($model->menu) ? $model->title_menu : Html::tag('strike', $model->title_menu); ?>
        </a>


    </li>
<?php elseif ($model->menu): ?>
    <li class="<?php if ($model->active) echo "active"; ?>">
        <?= Html::a($model->title_menu, $model->url, ["data-pjax" => "0"]) ?>
    </li>
<?php endif; ?>

