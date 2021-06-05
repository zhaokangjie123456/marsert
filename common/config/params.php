<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,

    'upload-activate' => 'on',
    'domain' => [
        'www' => 'http://backend.youjingxi.wang/v1',
        'm' => 'http://backend.youjingxi.wang/v1',
        'web' => 'http://backend.youjingxi.wang/v1',
        '1'=>'L0ra1Ow21yTBPRPQFUC9NdAmkfYyzf4a46do2UkVhKE',
    ],
//    'upload' => [
//        'avatar' => '/uploads/avatar',
//        'brand' => '/uploads/brand',
//        'book' => '/uploads/book',
//    ],
    //微信公众号开发
    'wechat' => [
        //微信公众号的基本配置里的 令牌token
        'token' => 'ASDqwer123456',
//        'appSecret'=>'85900e03e2fe9386be5064f516c55c7f',
        //微信公众号的基本配置里的 开发者id
        'appid' => 'wxa3420cba46f9e2eb',
        //服务器启用时的key  获取access_token 时会用到
        'sk' => '85900e03e2fe9386be5064f516c55c7f',
//        'sk' => 'e10955db8fc853d0940e61275114573d',
        //token验证通过之后的 (EncodingAESKey)
        'aeskey' => 'L0ra1Ow21yTBPRPQFUC9NdAmkfYyzf4a46do2UkVhKE',
        //支付要用到的参数
        'key'=>'d1iokr2qfq5e3hes4kc1grhv1ovwxggs',
        //商户号
        'mchid'=>'1543476981',
        //支付成功之后的回调地址自己控制器里的方法
        'notifyUrl'=>'http://backend.youjingxi.wang/v1/wechat/notify',
        'url'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
    ]
];
