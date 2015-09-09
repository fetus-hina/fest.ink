<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\Fest;
use app\models\OfficialData;

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

        Yii::$app->getResponse()->format = 'json';
        $now = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $alpha = $fest->alphaTeam;
        $bravo = $fest->bravoTeam;
        return [
            'now'   => $now,
            'id'    => $fest->id,
            'name'  => $fest->name,
            'term'  => [
                'begin' => $fest->start_at,
                'end'   => $fest->end_at,
                'in_session' => ((int)$fest->start_at <= $now && $now < (int)$fest->end_at),
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
                function (OfficialData $data) {
                    $alpha = $data->alpha;
                    $bravo = $data->bravo;
                    return [
                        'at'    => $data->downloaded_at,
                        'alpha' => $alpha ? $alpha->count : 0,
                        'bravo' => $bravo ? $bravo->count : 0,
                    ];
                },
                $fest->officialDatas
            ),
        ];
    }
}
