<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use DateTime;
use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use app\components\Version;
use app\models\Fest;
use app\models\Timezone;

class IndexJsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $tz = $request->get('tz');
        if (!is_scalar($tz) || !Timezone::findOne(['zone' => $tz])) {
            $tz = Yii::$app->timeZone;
        }

        $time2str = function ($time) use ($tz) {
            $t1 = (int)floor((float)$time); // time の整数部
            $t2 = (float)$time - $t1;       // time の小数部

            $dateTime = DateTime::createFromFormat(
                'U u',
                sprintf('%d %06d', $t1, (int)floor($t2 * 1000000))
            );
            $dateTime->setTimeZone(new DateTimeZone($tz));
            return $t2 > 0
                ? $dateTime->format('Y-m-d\TH:i:s.uP')
                : $dateTime->format('Y-m-d\TH:i:sP');
        };

        $now = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        Yii::$app->getResponse()->format = 'json';
        return [
            'now' => $now,
            'now_s' => $time2str($now),
            'source' => [
                'name'      => Yii::$app->name,
                'url'       => Yii::$app->getUrlManager()->createAbsoluteUrl(['/fest/index']),
                'version'   => Version::getVersion(),
                'revision'  => [
                    Version::getRevision(),
                    Version::getShortRevision(),
                ],
            ],
            'fests' => array_map(
                function ($fest) use ($now, $time2str) {
                    $alpha = $fest->alphaTeam;
                    $bravo = $fest->bravoTeam;
                    if ((int)$fest->start_at > $now) {
                        $state = 'scheduled';
                    } elseif ((int)$fest->end_at > $now) {
                        $state = 'in session';
                    } else {
                        $state = 'closed';
                    }
                    return [
                        'id'    => (int)$fest->id,
                        'name'  => $fest->name,
                        'term'  => [
                            'begin' => (int)$fest->start_at,
                            'end'   => (int)$fest->end_at,
                            'begin_s' => $time2str((int)$fest->start_at),
                            'end_s'   => $time2str((int)$fest->end_at),
                            'in_session' => ($state === 'in session'),
                            'status' => $state,
                        ],
                        'teams' => [
                            'alpha' => [
                                'name' => $alpha->name,
                                'ink' => $alpha->ink_color,
                            ],
                            'bravo' => [
                                'name' => $bravo->name,
                                'ink' => $bravo->ink_color,
                            ],
                        ],
                    ];
                },
                Fest::find()->orderBy('{{fest}}.[[id]] DESC')->all()
            ),
        ];
    }
}
