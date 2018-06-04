<?php

namespace floor12\pages\assets;

use yii\web\AssetBundle;

class SummernoteAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'summernote/dist/summernote.css',
        'codemirror/lib/codemirror.css',
        'codemirror/theme/monokai.css',

    ];
    public $js = [
        'summernote/dist/summernote.js',
        'summernote/dist/lang/summernote-ru-RU.min.js',
        'codemirror/lib/codemirror.js',
        'codemirror/mode/xml/xml.js',
    ];
    public $depends = [

    ];
}

