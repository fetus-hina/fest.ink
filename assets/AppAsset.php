<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use app\assets\FlotAsset;
use app\assets\FontAwesomeAsset;
use app\assets\TwitterWidgetAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapThemeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/fest.ink';
    public $css = [
        'fest.css',
    ];
    public $js = [
        'fest.js',
    ];
    public $jsOptions = [
        'async' => true,
    ];
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        BootstrapThemeAsset::class,
        FlotAsset::class,
        FontAwesomeAsset::class,
        JqueryAsset::class,
        TwitterWidgetAsset::class,
    ];
}
