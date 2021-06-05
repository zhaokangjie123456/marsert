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
        'request' => [
            'csrfParam' => '_csrf-backend',
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
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'trace'],
                    'logFile' => '@runtime/logs/'.date('Ym').'/app.trace.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'logFile' => '@runtime/logs/'.date('Ym').'/app.info.log',
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
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/wechat',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'valid',//
                        'GET' => 'login',//用户登录
                        'GET accesstoken' => 'accesstoken',//用户授权token
                        'POST userinfo' => 'userinfo',//获取用户信息
                        'POST pay' => 'pay',//微信支付接口
                        'POST notify' => 'notify',//接收微信发送的异步支付结果通知
                        'POST config' => 'config',//获取支付配置项
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/wexin',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST refund' => 'refund',//退款退货接口
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/out',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST refund' => 'refund',//退款没有发货接口
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/goods',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'show', //所有商品列表 带筛选 升降价格 升降销量 分类筛选
                        'GET' => 'data', //所有商品列表 带筛选 升降价格 升降销量 分类筛选
                        'GET' => 'list', //商品详情
                        'POST' => 'num', //点击喜欢数量+1 传like 商品id
                        'GET' => 'log', //点击喜欢数量+1 传like 商品id
                        'GET'=>'nihao',
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/merchant',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'orders', //生成订单 立即购买 普通用户 正在使用 直接在库存表里查询哪个加盟商有此规格的商品
                        'GET' => 'show', //订单详情  普通用户  order_code openid
                        'GET' => 'user', //订单列表  openid
                        'POST'=> 'increase',//加盟商添加快递单号 改变状态
                        'POST'=> 'purchase',  //立即购买生成订单 需要传很多值 匹配地址 没有用啦 指不定什么时候就用呢
                        'POST'=> 'quan',//全选生成订单 openid cart_id subtotal address_id
                        'GET'=>'userinfo', //个人中心
                        'GET'=>'status', //订单状态列表 需要传订单状态的值
                        'GET'=>'hair',//加盟商发货列表
                        'GET'=>'details',//加盟商发货详情
                        'POST'=>'apply',//普通用户提交退款退货申请
                        'POST' => 'image',//上传图片
                        'GET'=>'retreat', //普通用户退款退货申请列表加盟商
                        'GET'=>'goods', //普通用户加盟商退款详情
                        'POST'=>'trial', //加盟商审核普通用户提交的退款申请
                        'GET'=>'outlist', //加盟商审退款列表 普通用户
                        'GET'=>'outdetails', //加盟商退款详情 普通用户
                        'POST'=> 'ordinary',//普通用户添加快递单号
                        'POST'=> 'outupdate',//加盟商如果拒绝之后普通用户可以二次发起申请
                        'POST'=> 'confirm',//普通用户 加盟商 正常买货之后确认收获
                        'POST'=> 'outconfirm',//普通用户 退款退货之后加盟商确认收货
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/platform',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'order', //生成订单 加盟商
                        'POST' => 'card', //普通用户转换为加盟商
                        'POST' => 'express', //管理员添加快递单号  加盟商退货填写快递单号
                        'POST' => 'purchase', //立即购买生成订单 需要传啥不知道
                        'GET' => 'show', //订单详情  加盟商
                        'GET' => 'user', //订单列表  加盟商
                        'GET' =>'expre',//快递100
                        'GET' =>'list',//快递公司列表
                        'GET'=>'userinfo', //个人中心
                        'GET'=>'status', //订单状态列表 需要传订单状态的值
                        'GET'=>'stock',//加盟商的库存
                        'GET'=>'balance',//加盟商的余额 余额是发货完成多少单按照每单的金额计算
                        'GET'=>'moneylist',//加盟商的余额 余额是发货完成多少单按照每单的金额计算
                        'POST'=>'money',//加盟商提现申请
                        'POST'=>'pay',//加盟商使用余额支付

                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/cart',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST'=> 'increase',//添加 购物车
                        'GET' => 'list', //购物车列表
                        'POST' => 'except',//删除  传id
                        'POST'=> 'plus',//加商品数量 传商品id
                        'POST' => 'modify', //修改 商品分类
                        'POST'=> 'reduce',//减商品数量 传商品id
                        'POST'=> 'subtotal',//购物车小计  需要传商品id
                        'POST'=> 'single'//多选删除
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/address',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'increase', //添加收货地址  province city district address
                        'GET' =>   'list', //地址列表
                        'POST' => 'except',//删除  传id
                        'POST' => 'modify', //修改地址  province city district address
                        'POST' => 'default', //修改地址为默认 传id
                        'GET' => 'show', //地址详细信息 传id
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/timing',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET' => 'test', //一小时没付款自动删除此订单
                        'GET' => 'confirm', //15天没有确认收获之后就自动确认收货
                        'GET' => 'order', //加盟商如果3天不发货就加入黑名单然后从新分配给别的加盟商
                        'GET' => 'porder', //加盟商一小时没付款自动删除此订单
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/bank',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST' => 'increase', //加盟商添加银行信息 openid bank address name phone
                        'POST' => 'modify', //加盟商修改银行信息
                        'POST' => 'except',// 加盟商删除银行信息
                        'GET' => 'show',//银行信息列表  openid
                        'GET'=>'details', //银行信息列表详情  openid id
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
