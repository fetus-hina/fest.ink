<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\timezone;

use DateTime;
use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Timezone;

class JsonAction extends BaseAction
{
    public function run()
    {
        $now = new DateTime(
            sprintf('@%d', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time())
        );
        Yii::$app->getResponse()->format = 'json';
        return array_map(
            function (Timezone $tz) use ($now) {
                $tzInfo = new DateTimeZone($tz->zone);
                $offset = $tzInfo->getOffset($now);
                return [
                    'id' => $tz->zone,
                    'offset' => $now->setTimeZone($tzInfo)->format('P'),
                    'location' => $tzInfo->getLocation(),
                ];
            },
            Timezone::find()->all()
        );
    }
}
