<?php


namespace common\sdk;

use yii;
class PayController extends Controller
{
    public function actionIndex()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
        ];

        $alipay = Yii::$app->pay->getAlipay()->web($order); // 电脑支付
        // $alipay = Yii::$app->pay->getAlipay()->wap($order); // 手机网站支付
        // $alipay = Yii::$app->pay->getAlipay()->app($order); // APP 支付
        // $alipay = Yii::$app->pay->getAlipay()->pos($order); // 刷卡支付
        // $alipay = Yii::$app->pay->getAlipay()->scan($order); // 扫码支付
        // $alipay = Yii::$app->pay->getAlipay()->transfer($order); // 帐户转账
        // $alipay = Yii::$app->pay->getAlipay()->mini($order); // 小程序支付

        return $alipay->send();
    }

    public function actionReturn()
    {
        $data = Yii::$app->pay->getAlipay()->verify();

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    public function actionNotify()
    {
        $alipay = Yii::$app->pay->getAlipay();

        try{
            $data = $alipay->verify();

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Yii::$app->pay->getLog()->debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();
    }
}