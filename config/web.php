<?php

declare(strict_types=1);

use app\components\web\AssetConverter;
use app\components\web\AssetManager;
use app\components\web\PrettyJsonResponseFormatter;
use app\components\web\Response;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\caching\FileCache;
use yii\debug\Module as DebugModule;
use yii\gii\Module as GiiModule;
use yii\log\FileTarget;
use yii\smarty\ViewRenderer as SmartyViewRenderer;
use yii\web\JqueryAsset;

return (function (): array {
    $params = require(__DIR__ . '/params.php');
    $config = [
        'name' => 'イカフェスレート',
        'version' => '2.8.6',
        'id' => 'basic',
        'language' => 'ja-jp',
        'timeZone' => 'Asia/Tokyo',
        'basePath' => dirname(__DIR__),
        'bootstrap' => ['log'],
        'components' => [
            'assetManager' => [
                'class' => AssetManager::class,
                'appendTimestamp' => false,
                'bundles' => [
                    JqueryAsset::class => [
                        'js' => [
                            'jquery.min.js',
                        ],
                    ],
                    BootstrapAsset::class => [
                        'css' => [
                            'css/bootstrap.min.css',
                        ],
                    ],
                    BootstrapPluginAsset::class => [
                        'js' => [
                            'js/bootstrap.min.js',
                        ],
                    ],
                ],
                'converter' => [
                    'class' => AssetConverter::class,
                ],
            ],
            'urlManager' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'enableStrictParsing' => true,
                'rules' => [
                    '<id:\d+>'      => 'fest/view',
                    '<id:\d+>.json' => 'fest/view-json',
                    'timezone.json' => 'timezone/json',
                    'timezone/set'  => 'timezone/set',
                    '<action:\w+>'  => 'site/<action>',
                    'flash.json'    => 'fest/emulate-official-json',
                    'index.json'    => 'fest/index-json',
                    ''              => 'fest/index',
                ],
            ],
            'request' => [
                'cookieValidationKey' => include(__DIR__ . '/cookie-secret.php'),
                'trustedHosts' => [
                    // https://www.cloudflare.com/ips/
                    '103.21.244.0/22',
                    '103.22.200.0/22',
                    '103.31.4.0/22',
                    '104.16.0.0/12',
                    '108.162.192.0/18',
                    '131.0.72.0/22',
                    '141.101.64.0/18',
                    '162.158.0.0/15',
                    '172.64.0.0/13',
                    '173.245.48.0/20',
                    '188.114.96.0/20',
                    '190.93.240.0/20',
                    '197.234.240.0/22',
                    '198.41.128.0/17',
                    '2400:cb00::/32',
                    '2405:8100::/32',
                    '2405:b500::/32',
                    '2606:4700::/32',
                    '2803:f800::/32',
                    '2a06:98c0::/29',
                    '2c0f:f248::/32',
                ],
                'secureHeaders' => [
                    'CDN-Loop',
                    'CF-Connecting-IP',
                    'CF-IPCountry',
                    'CF-RAY',
                    'CF-Visitor',
                    'True-Client-IP',
                    'X-Forwarded-For',
                    'X-Forwarded-Proto',
                ],
                'ipHeaders' => [
                    'True-Client-IP',
                    'CF-Connecting-IP',
                    'X-Forwarded-For',
                ],
            ],
            'response' => [
                'class' => Response::class,
                'formatters' => [
                    'json' => PrettyJsonResponseFormatter::class,
                ],
            ],
            'view' => [
                'renderers' => [
                    'tpl' => [
                        'class' => SmartyViewRenderer::class,
                        'options' => [
                            'force_compile' => defined('YII_DEBUG') && YII_DEBUG,
                            'left_delimiter' => '{{',
                            'right_delimiter' => '}}',
                        ],
                    ],
                ],
            ],
            'cache' => [
                'class' => FileCache::class,
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
            'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => FileTarget::class,
                        'levels' => [
                            'error',
                            'warning',
                        ],
                    ],
                ],
            ],
            'db' => require(__DIR__ . '/db.php'),
        ],
        'params' => $params,
        'aliases' => [
            '@bower' => '@app/node_modules',
            '@node' => '@app/node_modules',
            '@statink' => 'https://stat.ink/',
        ],
    ];

    if (YII_ENV_DEV) {
        $config['bootstrap'][] = 'gii';
        $config['modules']['gii'] = [
            'class' => GiiModule::class,
        ];

        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => DebugModule::class,
            'allowedIPs' => file_exists(__DIR__ . '/debug-ips.php')
                ? require(__DIR__ . '/debug-ips.php')
                : [],
        ];
    }

    return $config;
})();
