<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/@fortawesome/fontawesome-free';
    public $js = [
        'js/all.min.js',
    ];
}
