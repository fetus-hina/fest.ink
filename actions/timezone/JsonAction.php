<?php
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
