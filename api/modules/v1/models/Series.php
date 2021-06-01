<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "series".
 *
 * @property int $id 自增
 * @property string $name 系列名称
 * @property int $image 系列封面图
 * @property string $details 详情图片 可以是数组
 * @property int $stock 库存数量
 * @property string $price 价格
 * @property int $goods_id 对应的商品id goods表
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Series extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'series';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'image', 'details', 'stock', 'price', 'goods_id'], 'required'],
            [['image', 'stock', 'goods_id'], 'integer'],
            [['price'], 'number'],
            [['create_at', 'update_at'], 'safe'],
            [['name', 'details'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'image' => 'Image',
            'details' => 'Details',
            'stock' => 'Stock',
            'price' => 'Price',
            'goods_id' => 'Goods ID',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
}
