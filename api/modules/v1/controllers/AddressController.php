<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Address;
use common\base\BaseController;
use yii;
class AddressController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\Address';
//    public $modelClass = 'api\modeules\v1\models\Image';
    /**
     * 用户添加地址
     *
     * */
    public function actionAddre()
    {
        $id = yii::$app->user->getId();
        var_dump($id);
        $model = new Address();
        $params = yii::$app->request->post();
        return $model->addre($params);
    }
    /**
     * 用户修改地址
     * */
    public function actionModify()
    {
        $model = new Address();
        $params = yii::$app->request->post();
        return $model->modify($params);
    }
    /**
     * 地址列表
     * */
    public function actionShow()
    {
        $model = new Address();
        $params = yii::$app->request->get();
        return $model->show($params);
    }
    /**
     * 删除地址信息  actionStrick
     * */
    public function actionStrick()
    {
        $model = new Address();
        $params = yii::$app->request->post();
        return $model->strick($params);
    }
    /**
     * 地址详情
     * */
    public function actionDetails()
    {
        $model = new Address();
        $params = yii::$app->request->get();
        return $model->details($params);
    }
    /**
     * 二维码列表
     * */
    public function actionCode()
    {
        $model = new Address();
        $params = yii::$app->request->get();
        return $model->code($params);
    }
    /**
     * 添加
     * */
    public function actionRest()
    {
        $model = new Address();
        $params = yii::$app->request->get();
        return $model->rest($params);
    }
}
