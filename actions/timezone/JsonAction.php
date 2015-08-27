<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\timezone;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Timezone;

class JsonAction extends BaseAction
{
    public function run()
    {
        Yii::$app->getResponse()->format = 'json';
        return [
            'zones' => array_map(
                function (Timezone $tz) {
                    return [
                        'zone' => $tz->zone,
                    ];
                },
                Timezone::find()->all()
            ),
        ];
    }
}
