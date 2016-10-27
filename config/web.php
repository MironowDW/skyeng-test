<?php

use app\services\AccessTokenService;
use app\services\PermissionService;

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'sfsdfsdlkfsldfksldf',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'pluralize' => false,
                    'prefix' => 'api',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'test',
                    'pluralize' => false,
                    'prefix' => 'api',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'step',
                    'pluralize' => false,
                    'prefix' => 'api',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'attempt',
                    'pluralize' => false,
                    'prefix' => 'api',
                ],
                '/' => 'site/index',
            ],
        ],
        AccessTokenService::class => AccessTokenService::class,
        PermissionService::class => PermissionService::class,
    ],
    'params' => [],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
