<?php

namespace floor12\pages\widgets;

use floor12\files\components\PictureWidget;
use floor12\files\models\File;

class ContentPicture
{
    public static function run($content)
    {
        if (preg_match_all('/{{image:\s([a-zA-Z0-9]+),\s*width:\s([0-9%]+),\s*alt:\s([^}]+)}}/', $content, $mapMatches)) {
            foreach ($mapMatches[1] as $resultKey => $hash) {
                $widget = PictureWidget::widget([
                    'model' => File::findOne(['hash' => $hash]),
                    'alt' => $mapMatches[3][$resultKey],
                    'width' => $mapMatches[2][$resultKey],
                ]);
                $content = str_replace($mapMatches[0][$resultKey], $widget, $content);
            }
        }
        return $content;
    }
}