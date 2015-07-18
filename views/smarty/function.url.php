<?php
use Yii;

function smarty_function_url($params, &$smarty)
{
    $params[0] = $params['route'];
    unset($params['route']);
    return Yii::$app->getUrlManager()->createUrl($params);
}
