<?php

namespace floor12\pages\assets;

use yii\web\AssetBundle;

class PagesAsset extends AssetBundle
{
    public $sourcePath = '@vendor/floor12/yii2-module-pages/src/assets';

    public $css = [

    ];

    public $js = [
        'js/pages.js',
        'js/summernote.conf.js',
    ];
    public $depends = [

    ];
}

