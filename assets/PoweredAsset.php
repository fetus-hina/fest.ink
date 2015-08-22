<?php
namespace app\assets;

use yii\web\AssetBundle;

class PoweredAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/powered';
    public $css = [];
    public $js = [];
}
