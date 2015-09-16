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
use yii\web\NotFoundHttpException;
use app\models\Fest;
use app\models\OfficialData;
use app\models\Timezone;

class ViewJsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $id = $request->get('id');
        if (!is_scalar($id) ||
                !($fest = Fest::findOne(['id' => $id]))) {
            throw new NotFoundHttpException();
        }
        $tz = $request->get('tz');
        if (!is_scalar($tz) || !Timezone::findOne(['zone' => $tz])) {
            $tz = Yii::$app->timeZone;
        }
        $callback = $request->get('callback');
        if (!is_scalar($callback) || !preg_match('/^[A-Za-z0-9_.]+$/', $callback)) {
            $callback = null;
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
        if ((int)$fest->start_at > $now) {
            $state = 'scheduled';
        } elseif ((int)$fest->end_at > $now) {
            $state = 'in session';
        } else {
            $state = 'closed';
        }
        $alpha = $fest->alphaTeam;
        $bravo = $fest->bravoTeam;
        $data = [
            'now'   => $now,
            'now_s' => $time2str($now),
            'id'    => $fest->id,
            'name'  => $fest->name,
            'term'  => [
                'begin' => $fest->start_at,
                'end'   => $fest->end_at,
                'begin_s' => $time2str($fest->start_at),
                'end_s'   => $time2str($fest->end_at),
                'in_session' => ($state === 'in session'),
                'status' => $state,
            ],
            'teams'  => [
                'alpha' => [
                    'name' => $alpha->name,
                    'ink' => $alpha->ink_color,
                ],
                'bravo' => [
                    'name' => $bravo->name,
                    'ink' => $bravo->ink_color,
                ],
            ],
            'wins'   => array_map(
                function (OfficialData $data) use ($time2str) {
                    $alpha = $data->alpha;
                    $bravo = $data->bravo;
                    return [
                        'at'    => $data->downloaded_at,
                        'at_s'  => $time2str($data->downloaded_at),
                        'alpha' => $alpha ? $alpha->count : 0,
                        'bravo' => $bravo ? $bravo->count : 0,
                    ];
                },
                $fest->officialDatas
            ),
        ];
        if ($callback !== null) {
            Yii::$app->getResponse()->format = 'jsonp';
            return [
                'data' => $data,
                'callback' => $callback,
            ];
        } else {
            Yii::$app->getResponse()->format = 'json';
            return $data;
        }
    }
}
