<?php

namespace floor12\pages\assets;

use yii\web\AssetBundle;

class PagesAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/pages/assets';

    public $css = [

    ];

    public $js = [
        'js/pages.js',
        'js/summernote.conf.js',
    ];
    public $depends = [

    ];
}

