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
                ], ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/out',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'refund',//退款
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/image',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'show',//卡册列表
                        'GET' => 'details',//卡册详情
                        'GET' => 'payment',//用户积分
                        'GET' => 'card',//形象对应的卡册  card里的id
                        'GET' => 'number',//拥有的卡册  card里的id
                        'GET' => 'numb',//当前所有的星级卡册  card里的id
                        'GET' => 'cardbook',//更多里的二级页面  card里的id
                        'POST' => 'change',//拥有的卡册  card里的id
//更多------------------>game.youjingxi.net.cn/v1/image/show（get）--------->game.youjingxi.net.cn/v1/image/cardbook?id=1（id是show返回的）------->后面的页面与（卡册一览的详情页面一样）
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/number',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'lock',//乐观锁
//                        'GET' => 'details',//卡册详情
                        'GET'=>'show',//5000个号中抽取300个
                        'GET'=>'str',//5000个号中抽取300个
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/weekorder',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'order',//领取礼包
                        'GET'=>'show',//礼包列表
                        'GET'=>'details',//礼包详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/integral',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'singin',//签到
                        'POST' => 'change',//积分兑换
                        'POST'=>'payment',//有多少积分
                        'POST'=>'match',//NFC兑换
                        'POST'=>'whole',//集齐兑换
                        'POST'=>'exchange',//5星兑换  兑换优惠券 正在使用
                        'GET'=>'share',//分享到朋友圈
                        'GET'=>'friend',//发给朋友
                        'GET'=>'details',//卡片列表
                        'GET'=>'show',//优惠券列表
                        'GET'=>'detail',//优惠券详情
                        'GET'=>'ni',//优惠券详情
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/address',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'addre',//添加
                        'POST' => 'modify',//修改
                        'POST'=>'strick',//删除
                        'POST'=>'default',//更改默认
                        'GET'=>'show',//列表
                        'GET'=>'details',//详情
                        'GET'=>'code',//二维码列表
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/order',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'order',//创建订单
                        'POST' => 'strick',//删除订单
                        'GET'=>'show',//订单列表
                        'POST'=>'refund',//发起退款申请
                        'GET'=>'express',//快递信息
                        'GET'=>'details',//订单详情

                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/goods',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET'=>'show',//商品列表
                        'GET'=>'details',//商品详情
                        'GET'=>'max',//用户拥有的卡册
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
