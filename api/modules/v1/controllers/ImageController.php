<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Image;
use common\base\BaseController;
use yii;
class ImageController extends BaseController
{
    public $modelClass = 'api\modeules\v1\models\Image';
    /**
     * 首页列表
     * */
    public function actionShow()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**&
     * 详情
     * */
    public function actionDetails()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
    /**
     * 签到的天数 积分数量
     * */
    public function actionPayment()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->payment($params);
    }
    /**
     *卡册详细信息
     * */
    public function actionCard()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->card($params);
    }
    /**
     *拥有的卡册
     * */
    public function actionNumber()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->number($params);
    }
    /**
     * 不知道是个什么鬼
     * */
    public function actionNumb()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->numb($params);
    }
    /**
     *拥有的卡册
     * */
    public function actionChange()
    {
        $model = new Image();
        $params = yii::$app->request->post();
        return $model->change($params);
    }
    /**
     * 啥都不是
     * */
    public function actionCardbook()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->cardbook($params);
    }
    /**
     * 轮播
     * */
    public function actionRotation()
    {
        $model = new Image();
        $params = yii::$app->request->get();
        return $model->rotation($params);
    }
}
