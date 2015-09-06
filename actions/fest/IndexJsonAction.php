<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\components\Version;
use app\models\Fest;

class IndexJsonAction extends BaseAction
{
    public function run()
    {
        $now = isset($_SERVER['REQUEST_TIME']) ? (int)$_SERVER['REQUEST_TIME'] : time();
        Yii::$app->getResponse()->format = 'json';
        return [
            'now' => $now,
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
                function ($fest) use ($now) {
                    $alpha = $fest->alphaTeam;
                    $bravo = $fest->bravoTeam;
                    return [
                        'id'    => (int)$fest->id,
                        'name'  => $fest->name,
                        'term'  => [
                            'begin' => (int)$fest->start_at,
                            'end'   => (int)$fest->end_at,
                            'in_session' => ((int)$fest->start_at <= $now && $now <= (int)$fest->end_at),
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
                Fest::find()->orderBy('fest.id DESC')->all()
            ),
        ];
    }
}
