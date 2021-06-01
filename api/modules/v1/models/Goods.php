<?php

namespace api\modules\v1\models;

use common\base\ErrorException;
use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "goods".
 *
 * @property int $id 自增
 * @property string $goods_name 商品名称
 * @property int $stock 商品库存
 * @property string $image 图片id
 * @property int $frequency 用户可以购买的次数
 * @property int $buynum 单次购买的数量
 * @property string $details 商品描述
 * @property string $dete 商品详情图
 * @property string $price 商品价格
 * @property string $create_at 创建时间
 * @property string $update_at 修改时间
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_name', 'stock', 'image', 'frequency', 'buynum', 'style_id','nfc','style_num','dete', 'price'], 'required'],
            [['stock', 'frequency','style_id','nfc','style_num', 'buynum'], 'integer'],
            [['price'], 'number'],
            [['create_at', 'update_at'], 'safe'],
            [['goods_name', 'dete'], 'string', 'max' => 100],
            [['image'], 'string', 'max' => 45],
            [['details'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_name' => 'Goods Name',
            'stock' => 'Stock',
            'style_id' => 'Style Id',
            'nfc' => 'Nfc',
            'style_num' => 'Style Num',
            'image' => 'Image',
            'frequency' => 'Frequency',
            'buynum' => 'Buynum',
            'details' => 'Details',
            'dete' => 'Dete',
            'price' => 'Price',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    /**
     * 商品列表
     * */
    public function show($params)
    {
//        if(empty($params['openid'])){
//            throw new ErrorException('openid不能为空');
//        }
//        $openid = Wechat::openid($params['openid']);
////        return $a;
        $pagesize = !empty($params['size'])?$params['size']:10;
//        $max = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,a.duihuan,b.styimage_id,max(b.lerver)')
//            ->from('integral as a')
//            ->innerJoin('card b','b.id=a.card_id')
//            ->where('a.user_id=:user_id',['user_id'=>$openid->id])
//            ->andWhere('b.lerver=5')
//            ->groupBy('b.styimage_id')
//            ->all();
//        $count = count($max);
//        if($count != 7){
//            return '目前没有访问权限';
//        }
        $model = (new \yii\db\Query())->select('a.id,a.goods_name,a.stock,a.image,a.details,a.dete,a.price,a.create_at,b.id as bid,b.url')
            ->from('goods as a,oss as b')
            ->where('a.image=b.id');
        $pages = new Pagination(['totalCount'=>$model->count(),'pageSize'=>$pagesize]);
        $data = $model->offset($pages->offset)->limit($pages->limit)->all();

        return ['items'=>$data,'pages'=>$pages,];
    }
    /**
     * 商品详情
     * */
    public function details($params)
    {
        $openid = Wechat::openid($params['openid']);
        if(empty($params['id'])){
            throw new ErrorException('id不能为空');
        }
//        return $openid;
//        $max = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,a.duihuan,b.styimage_id,max(b.lerver)')
//            ->from('integral as a')
//            ->innerJoin('card b','b.id=a.card_id')
//            ->where('a.user_id=:user_id',['user_id'=>$openid->id])
//            ->andWhere('b.lerver=5')
//            ->groupBy('b.styimage_id')
//            ->all();
//        //$params = (new yii\db\Query())->
//        $count = count($max);
//        if($count != 7){
//            return '目前没有访问权限';
//        } //ni ddsabdu dasdj;asdjjsadffjds f￥
        $model = (new \yii\db\Query())->select('a.id,a.goods_name,a.stock,a.image,a.details,a.dete,a.price,a.create_at,b.id as bid,b.url')
            ->from('goods as a,oss as b')
            ->where('a.id=:id',['id'=>$params['id']])
            ->one();
//        return $model;
        $array = explode(',',$model['dete']);
        foreach ($array as $key=>$value){
            $data[]=(new \yii\db\Query())->select('id,url')
                ->from('oss')
                ->where('id=:id',['id'=>$value])
                ->all();
        }
        $sale = (new \yii\db\Query())->select('id,goods_id')
            ->from('game_order')
            ->where('goods_id=:goods_id',['goods_id'=>$model['id']])
            ->count();
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $data);
        return ['items'=>$model,'details'=>$result,'sale'=>$sale];
    }
    /**
     * 用户的卡册数量
     * */
    public function max($params)
    {
        $openid = Wechat::openid($params['openid']);
        $max = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,a.duihuan,b.styimage_id,max(b.lerver)')
            ->from('integral as a')
            ->innerJoin('card b', 'b.id=a.card_id')
            ->where('a.user_id=:user_id', ['user_id' => $openid->id])
            ->andWhere('b.lerver=5')
            ->groupBy('b.styimage_id')
            ->count();
        $config = (new \yii\db\Query())->select('goods')
            ->from('config')
            ->one();
        if ($max < $config) {
            throw new ErrorException('没有购买资格');
        }
        return $max;
    }

}
