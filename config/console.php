<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'name' => 'イカフェスレート',
    'id' => 'basic-console',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => array_values(
        array_filter(
            ['log', YII_ENV === 'prod' ? null : 'gii'],
            fn (?string $module) => $module !== null,
        ),
    ),
    'controllerNamespace' => 'app\commands',
    'modules' => YII_ENV === 'prod' ? [] : ['gii' => 'yii\gii\Module'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
