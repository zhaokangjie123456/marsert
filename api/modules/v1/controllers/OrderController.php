<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\GameOrder;
use common\base\BaseController;
use yii;
class OrderController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\GameOrder';
    /**
     * 生成订单
     * */
    public function actionOrder()
    {
        $model = new GameOrder();
        $params = yii::$app->request->post();
        return $model->order($params);
    }
    /**
     * 删除订单
     * */
    public function actionStrick()
    {
        $model = new GameOrder();
        $params = yii::$app->request->post();
        return $model->strick($params);
    }
    /**
     * 订单列表
     * */
    public function actionShow()
    {
        $model = new GameOrder();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**
     * 详情
     * */
    public function actionDetails()
    {
        $model = new GameOrder();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
    /**
     * 快递信息
     * */
    public function actionExpress()
    {
        $ecpre = new GameOrder();
        $param = yii::$app->request->get();
        return $ecpre->express($param);
    }
    /**
     * 发起退款申请
     * */
    public function actionRefund()
    {
        $ecpre = new GameOrder();
        $param = yii::$app->request->post();
        return $ecpre->refund($param);
    }
}
