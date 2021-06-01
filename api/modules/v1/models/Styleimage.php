<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "styleimage".
 *
 * @property int $id 自增
 * @property int $image_id 形象id
 * @property string $type 形象具体的颜色款式
 * @property int $st_num 开始编号
 * @property int $en_num 结束编号
 * @property string $prefix 编号前缀
 * @property string $image 图片
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Styleimage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'styleimage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'type', 'st_num', 'image','en_num', 'prefix'], 'required'],
            [['image_id', 'image','st_num', 'en_num'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['type'], 'string', 'max' => 100],
            [['prefix'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image_id' => 'Image ID',
            'type' => 'Type',
            'st_num' => 'St Num',
            'en_num' => 'En Num',
            'image' => 'Image',
            'prefix' => 'Prefix',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
}
