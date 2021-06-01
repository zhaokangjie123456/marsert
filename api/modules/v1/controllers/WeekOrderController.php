<?php

namespace api\modules\v1\controllers;
use api\modules\v1\models\WeekOrder;
use common\base\BaseController;
use yii;
class WeekOrderController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\WeekOrder';
    /**
     * 用户领取礼包
     * */
    public function actionOrder()
    {
        $model = new WeekOrder();
        $params = yii::$app->request->post();
        return $model->order($params);
    }
    /**
     * 订单列表
     * */
    public function show()
    {
        $model = new WeekOrder();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**
     * 详情
     * */
    public function actionDetails()
    {
        $model = new WeekOrder();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
}
