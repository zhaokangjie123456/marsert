<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$main = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'class' => 'common\base\MyResponse',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $action = yii::$app->request->get('action', '');
                if ($action) {
                    $response->data = $response->getData();
                } else {
                    $response->data = [
                        'code' => $response->getCode(),
                        'data' => $response->getData(),
                        'msg' => $response->getMsg(),
                    ];
                }
                $response->format = common\base\MyResponse::FORMAT_JSON;
            },
        ],

        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<id:\d+>'=>'<module>/<controller>/view',
                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>'=>'<module>/<controller>',
                '<module:\w+>'=>'<module>',
                //配置路由
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/wechat',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'accesstoken',//传code 获取openid session_key
                        'GET' => 'openid',//传  获取access_token
                        'POST' => 'user',//传  $nikname,$headimgurl,$sex
                        'POST' => 'mobile',// 获取手机号的
                        'POST' => 'users',// 获取手机号的
                        'POST' => 'pay',//支付 uid id订单id orderName
                        'POST' => 'notify',//支付完成之后的回调更新订单状态
                    ]
                ],

            ],
        ],

    ],
    'modules' => [
        'v1' => ['class' => 'api\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
return $main;
