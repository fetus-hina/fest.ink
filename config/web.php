<?php
date_default_timezone_set('Asia/Tokyo');
$params = require(__DIR__ . '/params.php');
$config = [
    'id' => 'basic',
    'language' => 'ja-jp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '<id:\d+>'      => 'fest/view',
                '<id:\d+>.json' => 'fest/json',
                ''              => 'fest/index',
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'mVawrUR8e91Cgx_oNrTornJ_lq-MehZ_',
        ],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    'pluginDirs' => [
                        '//smarty/',
                    ],
                    'options' => [
                        'autoload_filters' => [
                            'left_delimiter' => '{{',
                            'right_delimiter' => '}}',
                        ],
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
