<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Number;
use common\base\BaseController;
use yii;
class NumberController extends BaseController
{
    public $modelClass = 'api\modules\v1\models\Number';

    /**
     * 中奖几率
     * */
    public function actionPrize()
    {
        $model = new Number();
        $params = yii::$app->request->post();
        return $model->prize($params);
    }

    /**
     * 乐观锁
     * */
    public function actionLock()
    {
        $model = new Number();
        $params = yii::$app->request->get();
        return $model->lock($params);
    }

    /**
     *剔除300个
     * */
    public function actionShow()
    {
        $model = new Number();
        $params = yii::$app->request->get();
        return $model->show($params);
    }

    /**
     * 字符串反转
     * */
    public function actionStr($str)
    {
        for($i=1; $i<=strlen($str);$i++){
            echo substr($str,-$i,1);
        }
//        return;
    }
}