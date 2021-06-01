<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Wechat;
use api\sdk\Out;
use api\sdk\Wechart;
use Yii;
use yii\rest\ActiveController;
class OutController extends ActiveController
{

    public $modelClass = 'api\modules\v1\models\WechatUser';
    //微信服务接入时，服务器需授权验证  这个是给微信的  前端不需要
    public function actionValid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        //valid signature , option
        if($this->checkSignature($signature,$timestamp,$nonce)){
            echo $echoStr;
        }
    }
    //参数校验
    public function checkSignature($signature,$timestamp,$nonce)
    {
        $request = Yii::$app -> request;
        $signature = trim(  $request -> get("signature","") );
        $timestamp = trim(  $request -> get("timestamp","") );
        $nonce = trim(  $request -> get("nonce","") );
        $token = \Yii::$app->params['wechat']['token'];
        if (!$token) {
            echo 'TOKEN is not defined!';
        }else{
            $tmpArr = array($token, $timestamp, $nonce);
            // use SORT_STRING rule
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );

            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }

    }

    //用户授权接口：获取access_token、openId等；获取并保存用户资料到数据库 微信登录接口
    public function actionLogin()
    {
        $request = Yii::$app -> request;
        $scope = $request->get( "scope","snsapi_userinfo");
        $appid = \Yii::$app->params['wechat']['appid'];
        $redirect_uri = "http://backend.youjingxi.wang/v1/wechat/accesstoken";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=#wechat_redirect";
        return $this->redirect( $url );
    }
    /**
     * 获取用户信息并插入表中
     *
     * */
    public function actionAccesstoken()
    {
        $code = $_GET["code"];
        $state = $_GET["state"];
        $appid = Yii::$app->params['wechat']['appid'];
        $appsecret = Yii::$app->params['wechat']['sk'];

        $request_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        //获取token和openid成功，数据解析 上线时打开注释
        $access_token = $result['access_token'];
        $refresh_token = $result['refresh_token'];
        $openid = $result['openid'];
        //请求微信接口，获取用户信息
        $userInfo = $this->getUserInfo($access_token,$openid);
        $user_check = (new \yii\db\Query())->select('*')->from('wechat_user')->where(['openid'=>$openid])->one();
        if ($user_check) {
//           //更新用户资料
            $user =   Yii::$app->db->createCommand()->update(
                'wechat_user',
                [
                    'nickname' => $userInfo['nickname'],
                    'sex' => $userInfo['sex'],
                    'headimgurl' => $userInfo['headimgurl'],
                    'country' => $userInfo['country'],
                    'province' => $userInfo['province'],
                    'city' => $userInfo['city'],
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'state' => 0,
                    'updata_at'=>date('Y-m-d H:i:s')
                ],
                [
                    'id' => $user_check['id'],
                ])->execute();
            if ($user == true){
                $rest = (new \yii\db\Query())
                    ->select('*')
                    ->from('wechat_user')
                    ->where('openid=:openid', ['openid' => $result['openid']])
                    ->one();
                return $rest;
            }else{
                return '更新失败';
            }
        } else {
            //保存用户资料WechatUser
            $user = new Wechat();
            $user->nickname = $userInfo['nickname'];
            $user->sex = $userInfo['sex'];
            $user->headimgurl = $userInfo['headimgurl'];
            $user->country = $userInfo['country'];
            $user->province = $userInfo['province'];
            $user->city = $userInfo['city'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->openid = $openid;
            $user->state = 0;
            $user->save();
            $res = Yii::$app->db->getLastInsertID();
            //通过刚刚插入的id查询url
            $rest = (new \yii\db\Query())
                ->select('*')
                ->from('wechat_user')
                ->where('id=:id', ['id' => $res])
                ->one();
            return $rest;
        }
    }

    //从微信获取用户资料
    public function getUserInfo($access_token,$openid)
    {
        $request_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
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
            $user = WechatUser::find()->where(['openid'=>$openid])->one();
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

        if(isset($_REQUEST["uid"])&&isset($_REQUEST["oid"])&&isset($_REQUEST["totalFee"])&&isset($_REQUEST["orderName"])){
            //uid、oid
            $uid = $_REQUEST["openid"];
//	exit($_REQUEST["openid"]);
            $oid = $_REQUEST["oid"];
            //微信支付参数
            $appid = Yii::$app->params['wechat']['appid'];
            $mchid = Yii::$app->params['wechat']['mchid'];
            $key = Yii::$app->params['wechat']['key'];
            $notifyUrl = Yii::$app->params['wechat']['notifyUrl'];
            //商品订单参数
            $totalFee = $_REQUEST["totalFee"];
//            return $totalFee;
            $orderName = $_REQUEST["orderName"];
            //$timestamp= $this->actionValid();

            //支付打包
            $wx_pay = new WechatPay($mchid, $appid, $key,$notifyUrl);
            $package = $wx_pay->createJsBizPackage($uid, $totalFee,$orderName);
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
        $wx_pay = new WechatPay($mchid, $appid, $key);
        $package = $wx_pay->getSignPackage($url);
        $result['error'] = 0;
        $result['msg'] = '获取成功';
        $result['config'] = $package;

        return $result;
    }


    //微信退款
    public function actionRefund()
    {
        //isset($_REQUEST["totalFee"])&&
        if(isset($_REQUEST["orderNo"])&&isset($_REQUEST["openid"])&&isset($_REQUEST["id"])&&isset($_REQUEST["refundFee"])) {
//            $totalFee = $_REQUEST["totalFee"];//订单总金额
            $orderNo = $_REQUEST["orderNo"];//订单号
            $openid = $_REQUEST["openid"];//用户openid
            $id = $_REQUEST['id'];//订单id
            $refundFee = $_REQUEST['refundFee'];//退款金额
            //获取配置信息
            $appid = Yii::$app->params['wechat']['appid'];
            $mchid = Yii::$app->params['wechat']['mchid'];
            $key = Yii::$app->params['wechat']['key'];
            $wx_pay = new Out($mchid, $appid, $key, '');
            return $wx_pay->refund($orderNo,$openid,$id,$refundFee);
        }else{
            return '退款参数错误';
        }
    }
}
