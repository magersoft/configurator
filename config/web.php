<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '0PZ5yUlNuoDObwXI-mcUVr35ptstxdKH',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
//            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'configyiivue@gmail.com',
                'password' => 'configyiivue1488',
                'port' => '465',
                'encryption' => 'ssl',
            ],
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
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'currencyCode' => 'RUB',
            'numberFormatterOptions' => [
                \NumberFormatter::MIN_FRACTION_DIGITS => 0,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ],
            //'dateFormat' => 'php:d.m.Y',
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                '<controller:(about|login|categories|registration)>' => 'site/index',
//                '<controller:(\w|-)+>/' => '<controller>/index',
                '<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:[-A-Za-z]+>' => '<controller>/<action>',
                '<n:.*>' => 'site/index',
            ],
        ]
    ],
    'params' => $params,
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['magervlad@yandex.ru'],
        ]
    ]
];
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}
return $config;