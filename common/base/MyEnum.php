<?php

namespace common\base;
use yii2mod\enum\helpers\BaseEnum;

class MyEnum extends BaseEnum
{

    /**
     * @param $value
     * @return 返回值和描述
     */
    public static function getArrByValue($value){
        return [
            'desc' => self::getLabel($value),
            'value' => $value
        ];
    }
}