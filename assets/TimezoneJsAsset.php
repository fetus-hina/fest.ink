<?php
namespace app\assets;

use yii\web\AssetBundle;

class TimezoneJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/timezone-js/src';
    public $js = [
        'date.js',
    ];
    public $depends = [
        'app\assets\TimezoneDataAsset',
    ];
}
