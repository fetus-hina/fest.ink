<?php
namespace app\assets;

use yii\web\AssetBundle;

class TwitterWidgetAsset extends AssetBundle
{
    public $baseUrl = '//platform.twitter.com/';
    public $js = [
        'widgets.js',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
}
