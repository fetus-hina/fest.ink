<?php
namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\Fest;
use app\models\OfficialData;

class JsonAction extends BaseAction
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
        return [
            'id'    => $fest->id,
            'name'  => $fest->name,
            'term'  => [
                'begin' => $fest->start_at,
                'end'   => $fest->end_at,
            ],
            'teams'  => [
                'r' => $fest->redTeam->name,
                'g' => $fest->greenTeam->name,
            ],
            'wins'   => array_map(
                function (OfficialData $data) {
                    $red = $data->red;
                    $green = $data->green;
                    return [
                        'at'    => $data->downloaded_at,
                        'r'     => $red ? $red->count : 0,
                        'g'     => $green ? $green->count : 0,
                    ];
                },
                $fest->officialDatas
            ),
        ];
    }
}