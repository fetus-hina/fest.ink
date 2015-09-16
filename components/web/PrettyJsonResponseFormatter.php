<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use yii\helpers\Json;
use yii\web\JsonResponseFormatter;

class PrettyJsonResponseFormatter extends JsonResponseFormatter
{
    /**
     * @inheritdoc
     */
    protected function formatJson($response)
    {
        $response->getHeaders()
            ->set('Content-Type', 'application/json; charset=UTF-8')
            ->set('Access-Control-Allow-Origin', '*');
        if ($response->data !== null) {
            $response->content = Json::encode($response->data, JSON_PRETTY_PRINT);
        }
    }
}
