<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\GameOrder;
use api\modules\v1\models\Goods;
use common\base\BaseController;
use yii;
class GoodsController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\Goods';
    /**
     * 商品列表
     * */
    public function actionShow()
    {
        $model = new Goods();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**
     * 商品详情
     * */
    public function actionDetails()
    {
        $model = new Goods();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
    public function actionMax()
    {
        $model = new Goods();
        $params = yii::$app->request->get();
        return $model->max($params);
    }
    /**
     * 款式列表
     * */
    public function actionSeries()
    {
        $model = new Goods();
        $params = yii::$app->request->get();
        return $model->series($params);
    }
    /**
     * 款式详情
     * */
    public function actionSeriesde()
    {
        $model = new Goods();
        $params = yii::$app->request->get();
        return $model->seriesde($params);
    }
}
