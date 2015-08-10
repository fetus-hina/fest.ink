<?php
namespace app\assets;

use yii\web\AssetBundle;

class TimezoneDataAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/tz-data';
    public $js = [
        'tz-init.js',
    ];
}
