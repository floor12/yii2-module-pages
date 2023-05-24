<?php
/**
 * @var $this \yii\web\View
 * @var $models \floor12\files\models\File[]
 */

if (empty($models))
    return;

\floor12\files\assets\LightboxAsset::register($this);
?>


<ul class="page-images">
    <?php foreach ($models as $key => $model): ?>
        <li>
            <a href="<?= $model->getPreviewWebPath(2500) ?>" data-lightbox="page-gallery">
                <?= \floor12\files\components\PictureWidget::widget([
                    'model' => $model,
                    'alt' => 'Изображение №' . ++$key,
                    'width' => 250
                ]) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
