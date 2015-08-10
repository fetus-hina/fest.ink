<?php
namespace app\assets;

use yii\web\AssetBundle;

class GithubForkRibbonCssAsset extends AssetBundle
{
    public $sourcePath = '@bower/github-fork-ribbon-css';
    public $css = [
        'gh-fork-ribbon.css'
    ];
}
