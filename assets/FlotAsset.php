<?php
namespace app\assets;

use yii\web\AssetBundle;

class FlotAsset extends AssetBundle
{
    public $sourcePath = '@bower/flot';
    public $js = [
        'jquery.flot.js',
        'jquery.flot.stack.js',
        'jquery.flot.time.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\TimezoneJsAsset',
    ];
}
