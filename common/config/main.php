<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // ...
        'pay' => [
//            //微信配置6
//            'wxpay'=>[
//                'appid'=>'wxf97197639f361a6a',
//                'mch_id'=>'1589567371',
//                'api_key'=>'EfPjMRrERYWMlocDrVAfF7XTaCmZMSfl',
//                'secret'=>'f6b4a458d798d0c82c7e12b343006163',
//                'notifyUrl'=>"http://k3u1128909.zicp.vip/payment/wxnotify",
//                'refundNotifyUrl'=>""
//            ],
            'class' => 'Guanguans\YiiPay\Pay',
            'wechatOptions' => [
                'appid' => 'wxf97197639f361a6a', // APP APPID
//                'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
//                'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
                'mch_id' => '1589567371',
                'key' => 'EfPjMRrERYWMlocDrVAfF7XTaCmZMSfl',
                'notify_url' => 'http://xxxxxx.cn/notify.php',
                'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
                'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
                'log' => [ // optional
                    'file' => './logs/wechat.log',
                    'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
                    'type' => 'single', // optional, 可选 daily.
                    'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
                ],
                'http' => [ // optional
                    'timeout' => 5.0,
                    'connect_timeout' => 5.0,
                    // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
                ],
                // 'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
            ],


//            //支付宝支付配置
//            'alipay'           => [
//                'appId'      => '2021001167671235',//
//                'rsaPrivateKey'  => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDZca7yMauwJSALy7eqF1t4JNAvbwqakLbDDP36+68e8Ob+i5agFbYgB+KBE6WsGKbKJIimGxlJkwZrXuWtFHzc4C8fFe6JtBxBofC3GjcV0skCeb2vY36Nx1OzSKNte/z/1YhOvbn7JNL0KaXMvH0yb54iexkoyiYrICW6rjJYiNfSdF0mYSjT0KPKNqM/Gg4h1QIn55wYgsXybqEdQeN+iEpdu7bmMEBF6u5umPfz7XedC8p0chdIfq0147PLdzkgtB8gx/LOr4Cs5CUmmz6/8uUU7fniz00wWufeIQxl9hdi27XEGoUei+KJDX0hVEHuwcS6ZjnUcl4bUg3wJztjAgMBAAECggEBANRzXpsELNtNaqIbyLdHWvdoAIBX3eRmwCqS5xPyFIgGl6rcd66xh+CD06qJp7Ud4NhgGaMxluNr4znE3mLdQaIM5/1XUFj6wSDfOHGuC2QnLn5ctBhI/b1Io17n+cVe/zJ3T1afyLa4o+QrTPNctnYw2DCYVVylJeW84yAiUs5sF3aGOKUborD6tHOXjPxzGL1YRGfCuHUzRNMh8yqzEX/uITSG0YJL9tzlVttovpJWNWL3X7wxX+DSAvpeRd9MZYAt5TZXFrIJxxF1pyYKWJyvIWJOgcoVclNY04CYa9r/Y5nErTSHTyNVb6yPBxIoMAalfW6O/BkNcG4Io7o5fQECgYEA+4DWeUrNSsvHN67k2nomqj1zeMKkE5yg0ZZ/++eQzmFIxoRtiSnanxKtuBkIxP+69mI9njH6xSq+0IbQ/DWTzZqHJ3kjrwh8BRQU7eZ6sPSa/e7mzpDdUNIdDmrwsIXSu8qvutK6nnNK9HXpkMAi6xS/PN3/wKenYf7SPthuG88CgYEA3VTzzWxys2DVtuRjDaWbKFYMUwXLhzyipmukKVEsTNGC7srxyOK5s/io2vwjOLsOAxywex4xGX7oyVDoPczT66puk7hJwwi519ua62bSY+5d7HO/5Hkg8+OiYTdlJY2TDTJt96TJbwGXmDjCOsy51pmzMa4JrVc6w1NpWWFMKC0CgYEAhsaUKIudV+e76mse2LV84t3rc1ta8eeNhsNP8n41a2NWzItK56Y2Meigj3da67bfSgl7W7sM13wApV6Zv442loeoxc51AOdbDp2kWZiZRxrNtCbCKRAYiacPSxqjwPT16QzE/yjOOPsvv7EwaRGNpMyJbuTImUU+vZUAtgzldKECgYEAmNvpHYC+nuBL+Wo2duBfDjaPgG9KJ3ZbJvDEibyECfHQqbcD0ae0dXVQuRzV/oJBpSiVQhKR+nfJse/s1XG3EtW8VM08NDS8lTYYAmga+eVQfNNcPiDh/07BgL7PXmYunziq9hObAs1oszrP+egWmP3B4pe+GzdZYDklUxN9k2kCgYBX05pJCieHJIkH2nLsyo2JXblF4UiKGRxh9IDrSJUDz1tlha7eUKBOxaPcY+rPFaO8zhKj5rzcK8G6O4mYA7JWrJuOTgCXAxBMd/Q+Xm/3i+pPzJf6V8Zn3lX6wfDOPsBscyq2jz99EwPEcTxD/C2jHoHbJwY8RVHky2ZH0CJ+Zg==',//
//                'alipayrsaPublicKey'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlLo8QGWdhlqNIym1UdytW15uqStRxfGtTM5p+XZGnoiRJR92MR0HJoWQS9/i/52AL4a/doMDP+mZGuf/GzPkqL1XehDaCwMyD/ydfaL05pb/kGXHUbAPSQnASTM9WEwXPAoezZhzwjwEIWA2Qc5zThGYNTcpkfYO3znQRekxd3A2rkTSNWUS/iH9a+EmyAl8Ecyq057tb+dxpIzmIc53wK012gZt6dEJR24G4+dRnv5TKWxsKJk5ih1Uz0ZbGn3RTbwdJzrdipuuASyF3ty8OXWIjzRCI1+rky21QPJt/vuq/W+kdkB1NALZRdTaE9YSPiyIHKCB056almDRXqEm/QIDAQAB',
//                'notifyUrl'=>"http://k3u1128909.zicp.vip/payment/Alipaynotify",//回调地址
//                'refundNotifyUrl'=>""
//            ],
            'alipayOptions' => [
                'app_id' => '2021001167671235',
                'notify_url' => 'http://xxxxxx.cn/notify.php',
                'return_url' => 'http://xxxxxx.cn/return.php',
                'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlLo8QGWdhlqNIym1UdytW15uqStRxfGtTM5p+XZGnoiRJR92MR0HJoWQS9/i/52AL4a/doMDP+mZGuf/GzPkqL1XehDaCwMyD/ydfaL05pb/kGXHUbAPSQnASTM9WEwXPAoezZhzwjwEIWA2Qc5zThGYNTcpkfYO3znQRekxd3A2rkTSNWUS/iH9a+EmyAl8Ecyq057tb+dxpIzmIc53wK012gZt6dEJR24G4+dRnv5TKWxsKJk5ih1Uz0ZbGn3RTbwdJzrdipuuASyF3ty8OXWIjzRCI1+rky21QPJt/vuq/W+kdkB1NALZRdTaE9YSPiyIHKCB056almDRXqEm/QIDAQAB',
                 // 加密方式： **RSA2**
                'private_key' => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDZca7yMauwJSALy7eqF1t4JNAvbwqakLbDDP36+68e8Ob+i5agFbYgB+KBE6WsGKbKJIimGxlJkwZrXuWtFHzc4C8fFe6JtBxBofC3GjcV0skCeb2vY36Nx1OzSKNte/z/1YhOvbn7JNL0KaXMvH0yb54iexkoyiYrICW6rjJYiNfSdF0mYSjT0KPKNqM/Gg4h1QIn55wYgsXybqEdQeN+iEpdu7bmMEBF6u5umPfz7XedC8p0chdIfq0147PLdzkgtB8gx/LOr4Cs5CUmmz6/8uUU7fniz00wWufeIQxl9hdi27XEGoUei+KJDX0hVEHuwcS6ZjnUcl4bUg3wJztjAgMBAAECggEBANRzXpsELNtNaqIbyLdHWvdoAIBX3eRmwCqS5xPyFIgGl6rcd66xh+CD06qJp7Ud4NhgGaMxluNr4znE3mLdQaIM5/1XUFj6wSDfOHGuC2QnLn5ctBhI/b1Io17n+cVe/zJ3T1afyLa4o+QrTPNctnYw2DCYVVylJeW84yAiUs5sF3aGOKUborD6tHOXjPxzGL1YRGfCuHUzRNMh8yqzEX/uITSG0YJL9tzlVttovpJWNWL3X7wxX+DSAvpeRd9MZYAt5TZXFrIJxxF1pyYKWJyvIWJOgcoVclNY04CYa9r/Y5nErTSHTyNVb6yPBxIoMAalfW6O/BkNcG4Io7o5fQECgYEA+4DWeUrNSsvHN67k2nomqj1zeMKkE5yg0ZZ/++eQzmFIxoRtiSnanxKtuBkIxP+69mI9njH6xSq+0IbQ/DWTzZqHJ3kjrwh8BRQU7eZ6sPSa/e7mzpDdUNIdDmrwsIXSu8qvutK6nnNK9HXpkMAi6xS/PN3/wKenYf7SPthuG88CgYEA3VTzzWxys2DVtuRjDaWbKFYMUwXLhzyipmukKVEsTNGC7srxyOK5s/io2vwjOLsOAxywex4xGX7oyVDoPczT66puk7hJwwi519ua62bSY+5d7HO/5Hkg8+OiYTdlJY2TDTJt96TJbwGXmDjCOsy51pmzMa4JrVc6w1NpWWFMKC0CgYEAhsaUKIudV+e76mse2LV84t3rc1ta8eeNhsNP8n41a2NWzItK56Y2Meigj3da67bfSgl7W7sM13wApV6Zv442loeoxc51AOdbDp2kWZiZRxrNtCbCKRAYiacPSxqjwPT16QzE/yjOOPsvv7EwaRGNpMyJbuTImUU+vZUAtgzldKECgYEAmNvpHYC+nuBL+Wo2duBfDjaPgG9KJ3ZbJvDEibyECfHQqbcD0ae0dXVQuRzV/oJBpSiVQhKR+nfJse/s1XG3EtW8VM08NDS8lTYYAmga+eVQfNNcPiDh/07BgL7PXmYunziq9hObAs1oszrP+egWmP3B4pe+GzdZYDklUxN9k2kCgYBX05pJCieHJIkH2nLsyo2JXblF4UiKGRxh9IDrSJUDz1tlha7eUKBOxaPcY+rPFaO8zhKj5rzcK8G6O4mYA7JWrJuOTgCXAxBMd/Q+Xm/3i+pPzJf6V8Zn3lX6wfDOPsBscyq2jz99EwPEcTxD/C2jHoHbJwY8RVHky2ZH0CJ+Zg==',
                // 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
                // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
                // 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
                'log' => [ // optional
                    'file' => './logs/alipay.log',
                    'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
                    'type' => 'single', // optional, 可选 daily.
                    'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
                ],
                'http' => [ // optional
                    'timeout' => 5.0,
                    'connect_timeout' => 5.0,
                    // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
                ],
                // 'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
            ],
        ],

    ],
];
