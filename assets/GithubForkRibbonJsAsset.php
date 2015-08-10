<?php
namespace app\assets;

use yii\web\AssetBundle;

class GithubForkRibbonJsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/gh-fork-ribbon';
    public $js = [
        'gh-fork-ribbon.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\GithubForkRibbonCssAsset',
        'app\assets\FontAwesomeAsset',
    ];
}
