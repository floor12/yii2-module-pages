<?php

namespace floor12\pages\assets;

use floor12\editmodal\EditModalAsset;
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
        'rmrevin\yii\fontawesome\AssetBundle',
        'floor12\editmodal\EditModalAsset'
    ];
}

