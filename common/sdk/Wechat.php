<?php


namespace common\sdk;

use yii;
class Wechat extends Controller
{
    public function actionIndex()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **单位：分**
            'body' => 'test body - 测试',
            'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Yii::$app->pay->getWechat()->mp($order); // 公众号支付
        // $pay = Yii::$app->pay->getWechat()->miniapp($order); // 小程序支付
        // $pay = Yii::$app->pay->getWechat()->wap($order); // H5 支付
        // $pay = Yii::$app->pay->getWechat()->scan($order); // 扫码支付
        // $pay = Yii::$app->pay->getWechat()->pos($order); // 刷卡支付
        // $pay = Yii::$app->pay->getWechat()->app($order); // APP 支付
        // $pay = Yii::$app->pay->getWechat()->transfer($order); // 企业付款
        // $pay = Yii::$app->pay->getWechat()->redpack($order); // 普通红包
        // $pay = Yii::$app->pay->getWechat()->groupRedpack($order); // 分裂红包

        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function actionNotify()
    {
        $pay = Yii::$app->pay->getWechat();

        try{
            $data = $pay->verify();

            Yii::$app->pay->getLog()->debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send();
    }
}