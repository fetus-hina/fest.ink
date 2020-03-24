<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class IPVersionBadge extends Widget
{
    public function run()
    {
        if (!$version = $this->getIPVersion()) {
            return '';
        }

        return Html::encode(sprintf('via %s', $version));
    }

    private function getIPVersion(): ?string
    {
        $ipAddr = Yii::$app->request->userIP;
        if (!$ipAddr) {
            return null;
        }

        return (strpos($ipAddr, ':') === false)
            ? 'IPv4'
            : 'IPv6';
    }
}
