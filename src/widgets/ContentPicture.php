<?php

namespace floor12\pages\widgets;

use floor12\files\components\PictureWidget;
use floor12\files\models\File;
use yii\helpers\Html;

class ContentPicture
{
    /**
     * Заменяет теги {{image: HASH, width: 900, alt: Описание}} на <picture>.
     *
     * Необязательный параметр mobile — хеш отдельной картинки для узких экранов:
     * {{image: HASH, width: 900, alt: Описание, mobile: HASH2}}. Тогда в <picture>
     * первым идёт source с media="(max-width: 767px)", и браузер качает только одну из двух.
     */
    public static function run($content)
    {
        if (preg_match_all('/{{image:\s([a-zA-Z0-9]+),\s*width:\s([0-9%]+),\s*alt:\s([^}]+?)(?:,\s*mobile:\s*([a-zA-Z0-9]+))?\s*}}/', $content, $mapMatches)) {
            foreach ($mapMatches[1] as $resultKey => $hash) {
                $model = File::findOne(['hash' => $hash]);
                $mobile = $mapMatches[4][$resultKey] ? File::findOne(['hash' => $mapMatches[4][$resultKey]]) : null;
                $alt = $mapMatches[3][$resultKey];
                $width = $mapMatches[2][$resultKey];

                $widget = $model && $mobile && $model->isImage() && $mobile->isImage()
                    ? self::pictureWithMobile($model, $mobile, (int)$width, $alt)
                    : PictureWidget::widget([
                        'model' => $model,
                        'alt' => $alt,
                        'width' => $width,
                    ]);
                $content = str_replace($mapMatches[0][$resultKey], $widget, $content);
            }
        }
        return $content;
    }

    private static function pictureWithMobile(File $model, File $mobile, int $width, string $alt): string
    {
        $sources = '';
        foreach ([true, false] as $webp) {
            foreach ([[$mobile, '(max-width: 767px)'], [$model, null]] as [$file, $media]) {
                $sources .= Html::tag('source', '', [
                    'type' => $webp ? 'image/webp' : 'image/jpeg',
                    'media' => $media,
                    'srcset' => $file->getPreviewWebPath(1.5 * $width, $webp) . ' 1x, '
                        . $file->getPreviewWebPath(2 * $width, $webp) . ' 2x',
                ]);
            }
        }
        // alt вставляем как есть, как и PictureWidget: в контенте он уже прошёл через HtmlPurifier
        $img = '<img src="' . $model->getPreviewWebPath($width) . '" alt="' . $alt . '">';
        return Html::tag('picture', $sources . $img);
    }
}
