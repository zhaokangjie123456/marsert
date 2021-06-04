<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$main = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'authManager' =>[
            'class' =>'yii\rbac\DbManager',
            'itemTable' =>'{{auth_item}}',
            'itemChildTable' =>'{{auth_item_child}}',
            'ruleTable' =>'{{auth_rule}}',
            'assignmentTable' =>'{{auth_assignment}}',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        //清楚缓存
        'assetManager' => [
            'linkAssets' => true,
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
            'identityClass' => 'backend\modules\v1\models\WechatUser',
            'enableAutoLogin' => true,

        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'error'],
                    'logFile' => '@runtime/logs/'.date('Ym').'/app.error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Ym').'/app.warning.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            //地址严格模式
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<id:\d+>'=>'<module>/<controller>/view',
                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>'=>'<module>/<controller>',
                '<module:\w+>'=>'<module>',
                //配置路由

                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/login',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'show',//注册  传username password
                        'POST' => 'sign',//注册 传username password
                        'POST' => 'out',//注册 传token

                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/upload',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'image',//上传图片 或者视频
                        'GET' =>'strike',//删除图片
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/styleimage',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'style',//添加
                        'POST' =>'modify',//修改
                        'POST' =>'strick',//删除
                        'GET' =>'show',//列表
                        'GET' =>'details',//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/image',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'image',//添加
                        'POST' =>'modify',//修改
                        'POST' =>'strick',//删除
                        'GET' =>'show',//列表
                        'GET' =>'details',//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/number',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'show',//列表
                        'GET' =>'details',//用户列表
                        'GET' =>'card',//用户的卡册列表
                        'GET' =>'payment',//用户的积分 签到列表
                        'GET' =>'nfc',//用户的积分 签到列表
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/card',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'card',//压缩
                        'GET' =>'show',//列表
                        'POST' => 'modify',//修改
                        'POST'=>'strick',//删除
                        'GET'=>'details'//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/week',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'week',//礼包订单
                        'GET' =>'list',//列表
                        'POST' => 'modify',//修改
                        'POST'=>'strick',//删除
                        'GET'=>'details'//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/periphery',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'periphery',//添加礼包商品
                        'GET' =>'list',//列表
                        'POST' => 'modify',//修改
                        'POST'=>'strick',//删除
                        'GET'=>'details'//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/fication',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'fication',//添加礼包分类
                        'GET' =>'list',//列表
                        'POST' => 'modify',//修改
                        'POST'=>'strick',//删除
                        'GET'=>'details'//详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/rotation',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'rotation',//添加轮播
                        'GET' =>'show',//轮播列表
                        'POST' => 'modify',//修改轮播
                        'POST'=>'strick',//删除轮播
                        'GET'=>'details'//轮播详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/goods',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'goods',//列表
                        'POST' =>'modify',//用户列表
                        'POST' =>'strick',//用户的卡册列表
                        'GET' =>'list',//用户的积分 签到列表
                        'GET' =>'details',//用户的积分 签到列表
                        'GET' =>'series',//系列列表
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/order',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'refund',//管理员审核退款
                        'GET' =>'list',//快递公司列表
                        'GET' =>'refun',//退款列表
                        'POST' =>'express',//添加快递信息
                        'GET' =>'show',//订单列表
                        'GET' =>'details',//订单详情
                        'GET' =>'order',//快递信息
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/code',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'code',//添加二维码
                        'POST' =>'modify',//修改二维码
                        'GET' =>'show',//二维码列表
                        'GET' =>'details',//二维码详情详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/config',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'config',//添加达到什么程度可以添加地址
                        'POST' =>'modify',//修改二维码
                        'GET' =>'show',//配置列表
                        'GET' =>'details',//二维码详情详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/series',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'series',//添加达到什么程度可以添加地址
                        'POST' =>'modify',//修改二维码
                        'POST' =>'strick',//修改二维码
                        'GET' =>'show',//配置列表
                        'GET' =>'details',//二维码详情详情
                    ]
                ],
            ],
        ],

    ],
    'modules' => [
        'v1' => ['class' => 'backend\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
return $main;
