<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\GameOrder;
use api\modules\v1\models\Goods;
use api\modules\v1\models\Order;
use api\modules\v1\models\Wechat;
use api\sdk\Wechart;
use api\sdk\WechatPay;
use api\sdk\WXBizDataCrypt;
use common\base\ErrorException;
use yii;
use common\base\BaseController;
use yii\rest\ActiveController;

class WechatController extends ActiveController
{
    public $modelClass = 'backend\modules\v1\models\wechart';
    /**
     * 获取用户的openid 与session_key
     * */
    public function actionAccesstoken()
    {
        $code = $_GET["code"];
        $appid = Yii::$app->params['wechat']['appid'];
        $appsecret = Yii::$app->params['wechat']['appsecret'];
        $request_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        //转换json格式为数组
        $result = $this->response($result);
//        return $result;
        $model = Wechat::findOne(['openid'=>$result['openid']]);
        if($model == false){
            $user =  Yii::$app->db->createCommand()->insert(
                'wechat',
                [
                    'openid' =>$result['openid'],
                    'session_key' =>$result['session_key'],
                    'create_at'=>date('Y-m-d H:i:s'),
                ])->execute();
            if($user == true){
                $user = Yii::$app->db->getLastInsertID();
                $rest = (new \yii\db\Query())
                    ->select('openid,session_key')
                    ->from('wechat')
                    ->where('id=:id',['id'=>$user])
                    ->one();
                return $rest;
            }else{
                return '获取数据失败1';
            }
        }else{
            $sql = 'UPDATE wechat SET openid=:openid,session_key=:session_key WHERE id=:id';
            $data = \Yii::$app->db->createCommand($sql,[
                ':openid' =>$result['openid'],':session_key'=>$result['session_key'],
                ':id'=>$model->id
            ])->query();
            if($data == true){
                $rest = (new \yii\db\Query())
                    ->select('openid,session_key')
                    ->from('wechat')
                    ->where('id=:id',['id'=>$model->id])
                    ->one();
                return $rest;
            }else{
                return '获取数据失败';
            }
        }
    }
    /**
     * 获取access_token
     * */
    public function actionOpenid()
    {
        //使用Redis缓存 access_token
        $redis = Yii::$app->redis;
        $redis_token = $redis->get('wechat:access_token');
        if ($redis_token) {
            $access_token = $redis_token;
        } else {
            $appid = Yii::$app->params['wechat']['appid'];
            $appsecret = Yii::$app->params['wechat']['appsecret'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $res = json_decode(Wechart::curlGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $redis->set('wechat:access_token', $access_token);
                $redis->expire('wechat:access_token', 7000);
            }
        }
        return $access_token;
    }
    /**
     * 获取用户信息
     * */
    public function actionUser()
    {
        $model = Wechat::findOne(['openid'=>$_REQUEST['openid']]);
//        $nikname = urlencode($_REQUEST['nikname']);
        if($model == true){
            $sql = 'UPDATE wechat SET nikname=:nikname,headimgurl=:headimgurl WHERE id=:id';
            $data = \Yii::$app->db->createCommand($sql,[
                ':nikname' =>$_REQUEST['nikname'],':headimgurl'=>$_REQUEST['headimgurl'],
                ':id'=>$model->id
            ])->query();
            if($data == true){
                $rest = Wechat::findOne(['id'=>$model['id']]);
                return $rest;
            }else{
                return '获取信息失败';
            }
        }else{
            return 'openid不正确没有此用户';
        }

    }
    /**
     * 用户信息
     * */
    public function actionUsers()
    {
        $url = "https://developers.weixin.qq.com/miniprogram/dev/api/open-api/setting/SubscriptionsSetting.html";
        $res = json_decode(Wechart::curlGet($url));
        var_dump($res); ;
    }
    /**
     * 获取小程序手机号
     * */
    public function actionMobile()
    {
        $appid = Yii::$app->params['wechat']['appid'];
        $sessionKey = trim($_REQUEST['session_key']);
        $encryptedData = trim($_REQUEST['encryptedData']);
        $iv = trim($_REQUEST['iv']);
        $openid = $_REQUEST['openid'];
        $pc =  new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $result = $this->response($data);
//            return $result;
            $model = Wechat::findOne(['openid'=>$openid]);
            if($model == true){
                $sql = 'UPDATE wechat SET mobile=:mobile WHERE id=:id';
                $data = \Yii::$app->db->createCommand($sql,[
                    ':mobile' =>$result['phoneNumber'],
                    ':id'=>$model->id
                ])->query();
                if($data == true){
                    return '获取手机号成功';
                }else{
                    return '获取手机号失败';
                }
            }else{
                return 'openid不正确没有此用户';
            }
        } else {
            print($errCode . "\n");
        }

    }
    //从微信获取用户资料
    public function getUserInfo($access_token,$openid)
    {
        $request_url = 'https://api.weixin.qq.com/wxa/getpaidunionid?access_token='.$access_token.'&openid='.$openid.'';
//        $request_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //获取用户资料接口
    public function actionUserinfo()
    {
        if(isset($_REQUEST["openid"])){
            $openid = $_REQUEST["openid"];
            $user = Wechat::find()->where(['openid'=>$openid])->one();
            if ($user) {
                $result['error'] = 0;
                $result['msg'] = '获取成功';
                $result['user'] = $user;
            } else {
                $result['error'] = 1;
                $result['msg'] = '没有该用户';
            }
        } else {
            $result['error'] = 1;
            $result['msg'] = 'openid为空';
        }
        return $result;
    }

    private function response($text)
    {
        return json_decode($text, true);
    }

    //微信支付接口：打包支付数据
    public function actionPay()
    {
        //订单id  用户的openid
        if(isset($_REQUEST["id"]) &&isset($_REQUEST["openid"])){
            $open = Wechat::openid($_REQUEST['openid']);
            $order = GameOrder::findOne(['id'=>$_REQUEST['id'],'user_id'=>$open->id]);
            if($order == false){
                throw new ErrorException('无效的订单');
            }
            $goods = Goods::findOne(['id'=>$order->goods_id]);
            //uid、oid
            $openid = $open->openid;
            //订单号
            $outTradeNo = $order->order_code;
            //商品订单总金额
            $totalFee = $order->subtotal;
            //比如WowSurprise有惊喜
            $orderName = $goods->goods_name;
            //$timestamp= $this->actionValid();
//            return $outTradeNo;die;
            //微信支付参数  小程序的appid
            $appid = Yii::$app->params['wechat']['appid'];
            //商户号
            $mchid = Yii::$app->params['wechat']['mchid'];
            //支付时用到的key
            $key = Yii::$app->params['wechat']['key'];
            //支付成功的回调地址
            $notifyUrl = Yii::$app->params['wechat']['notifyUrl'];
            //支付打包
            $wx_pay = new WechatPay($mchid, $appid, $key,$notifyUrl);
            $package = $wx_pay->createJsBizPackage($openid, $totalFee,$orderName,$outTradeNo);
            $result['error'] = 0;
            $result['msg'] = '支付打包成功';
            $result['package'] = $package;
        }else{
            $result['error'] = 1;
            $result['msg'] = '请求参数错误';
        }
        return $result;
    }
    /*
     * 获取微信的配置文件
     *
     * */
    public function actionConfig()
    {
        $url =Yii::$app->params['wechat']['url'];
        //微信支付参数
        $appid = Yii::$app->params['wechat']['appid'];
        $mchid = Yii::$app->params['wechat']['mchid'];
        $key = Yii::$app->params['wechat']['key'];
        $notifyUrl = Yii::$app->params['wechat']['notifyUrl'];
        $wx_pay = new WechatPay($mchid, $appid, $key,$notifyUrl);
        $package = $wx_pay->getSignPackage($url);
        $result['error'] = 0;
        $result['msg'] = '获取成功';
        $result['config'] = $package;

        return $result;
    }
    //接收微信发送的异步支付结果通知
    public function actionNotify()
    {
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }
        $transaction = yii::$app->db->beginTransaction();
        try {
            $res = Yii::$app->db->createCommand()->update(
                'game_order',
                [
                    'pay_state'    => 2,
                    'pay_at'=>date('Y-m-d H:i:s'),
                ],
                [
                    'order_code' => $postObj->out_trade_no,
                ]
            )->execute();
            if($res == true){
               $transaction->commit();
               return '支付成功';
            }
        }catch(\Throwable $e){
            $transaction->rollBack();
            throw $e;
        }
        //微信支付参数
        $appid = Yii::$app->params['wechat']['appid'];
        $mchid = Yii::$app->params['wechat']['mchid'];
        $key = Yii::$app->params['wechat']['key'];
        $wx_pay = new WechatPay($mchid, $appid, $key);
        //验证签名
        $arr = (array)$postObj;
        unset($arr['sign']);
        if ($wx_pay->getSign($arr, $key) != $postObj->sign) {
            die("签名错误");
        }
    }
}
