<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace app\components\web;

use Yii;
use app\components\Version;
use yii\helpers\Url;

/**
 * @inheritdoc
 */
class AssetManager extends \yii\web\AssetManager
{
    /**
     * @inheritdoc
     */
    public function getAssetUrl($bundle, $asset, $appendTimestamp = null)
    {
        if (($actualAsset = $this->resolveAsset($bundle, $asset)) !== false) {
            if (strncmp($actualAsset, '@web/', 5) === 0) {
                $asset = substr($actualAsset, 5);
                $basePath = Yii::getAlias("@webroot");
                $baseUrl = Yii::getAlias("@web");
            } else {
                $asset = Yii::getAlias($actualAsset);
                $basePath = $this->basePath;
                $baseUrl = $this->baseUrl;
            }
        } else {
            $basePath = $bundle->basePath;
            $baseUrl = $bundle->baseUrl;
        }

        if (!Url::isRelative($asset) || strncmp($asset, '/', 1) === 0) {
            return $asset;
        }

        $withTimestamp = $this->appendTimestamp;
        if ($appendTimestamp !== null) {
            $withTimestamp = $appendTimestamp;
        }

        if ($withTimestamp) {
            foreach (['', '.gz'] as $appendExtension) {
                if (($timestamp = @filemtime("$basePath/{$asset}{$appendExtension}")) > 0) {
                    return "$baseUrl/$asset?v=$timestamp";
                }
            }
        }
        return "$baseUrl/$asset";
    }

    protected function hash($path)
    {
        if (is_callable($this->hashCallback)) {
            return call_user_func($this->hashCallback, $path);
        }

        $path = (is_file($path) ? dirname($path) : $path) . filemtime($path);

        return vsprintf('%s/%s', [
            Version::getShortRevision(),
            substr(hash('sha256', $path), 0, 16),
        ]);
    }
}
