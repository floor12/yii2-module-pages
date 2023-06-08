<?php

namespace floor12\pages\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class PagesAsset extends AssetBundle
{
    public $sourcePath = '@vendor/floor12/yii2-module-pages/src/assets';

    public $css = [
        'pages.css'
    ];

    public $js = [
        'autosubmit.js',
        'pages.js',
    ];
    public $depends = [
        'floor12\editmodal\EditModalAsset',
        JuiAsset::class
    ];
}

