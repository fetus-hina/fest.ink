<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Fest;
use app\models\Mvp;
use app\models\OfficialData;

class EmulateOfficialJsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $time = $request->get('t');
        if (!is_scalar($time) || !preg_match('/^\d+$/', $time)) {
            $time = (int)(isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME_FLOAT'] : time());
        } else {
            $time = (int)$time;
        }
        $callback = $request->get('callback');
        if (!is_scalar($callback) || !preg_match('/^[A-Za-z0-9_.]+$/', $callback)) {
            $callback = null;
        }

        // その時間に開催されている/いたフェスを取得
        $fest = Fest::find()
            ->andWhere(['<=', '[[start_at]]', $time])
            ->andWhere(['>', '[[end_at]]', $time])
            ->one();
        if (!$fest) {
            return $this->result([], $callback);
        }

        // その時間から10分以内に取得した最新の JSON に関する情報を取得
        $officialData = OfficialData::find()
            ->andWhere(['[[fest_id]]' => $fest->id])
            ->andWhere(['>', '[[downloaded_at]]', $time - 10 * 60])
            ->andWhere(['<=', '[[downloaded_at]]', $time])
            ->orderBy('[[downloaded_at]] DESC')
            ->limit(1)
            ->one();
        if (!$officialData) {
            return $this->result([], $callback);
        }

        // データ処理のためにアルファ/ブラボーチームの情報が必要
        $alpha = $fest->alphaTeam;
        $bravo = $fest->bravoTeam;
        if (!$alpha || !$bravo) {
            // 本当は Internal Server Error
            return $this->result([], $callback);
        }

        return $this->result(
            array_map(
                function (Mvp $mvp) use ($alpha, $bravo) {
                    $team = $mvp->color_id == 1 ? $alpha : $bravo;
                    return [
                        'win_team_name' => $team->name,
                        'win_team_mvp' => $mvp->name,
                    ];
                },
                $officialData->mvps
            ),
            $callback
        );
    }

    private function result(array $data, $callback) {
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
