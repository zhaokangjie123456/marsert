<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Integral;
use common\base\BaseController;
use yii;
class IntegralController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\Integral';
    /**
     *签到
     * */
    public function actionSingin()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->singin($params);
    }
    /**
     *积分兑换
     * */
    public function actionChange()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->change($params);
    }
    /**
     *有多少积分
     * */
    public function actionPayment()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->payment($params);
    }
    /**
     * nfc兑换
     * */
    public function actionMatch()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->match($params);
    }
    /**
     * 分享到朋友圈
     * */
    public function actionShare()
    {
        $model = new Integral();
        $params = yii::$app->request->get();
        return $model->share($params);
    }
    /**
     * 发送给朋友
     * */
    public function actionFriend()
    {
        $model = new Integral();
        $params = yii::$app->request->get();
        return $model->friend($params);
    }
    /**
     * 集齐一套兑换手办
     * */
    public function actionWhole()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->whole($params);
    }
    /**
     * 卡片列表
     * */
    public function actionDetails()
    {
        $model = new Integral();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
    /**
     * 优惠券详情
     * */
    public function actionDetail()
    {
        $model = new Integral();
        $params = yii::$app->request->get();
        return $model->detail($params);
    }
    /**
     * 优惠券列表
     * */
    public function actionShow()
    {
        $model = new Integral();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**
     * 5星兑换
     * */
    public function actionExchange()
    {
        $model = new Integral();
        $params = yii::$app->request->post();
        return $model->exchange($params);
    }
}
