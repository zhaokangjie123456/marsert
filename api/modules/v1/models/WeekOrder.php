<?php

namespace api\modules\v1\models;

use backend\modules\v1\models\Oss;
use backend\modules\v1\models\Periphery;
use common\base\ErrorException;
use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "week_order".
 *
 * @property int $id 自增
 * @property string $order 订单号
 * @property int $user_id 用户id
 * @property string $price 单价
 * @property string $subtotal 总价
 * @property int $num 商品数量
 * @property string $code 编号
 * @property int $order_id 商品订单id
 * @property int $pay_stat 支付状态 1未支付 2 已支付 3 已退款
 * @property int $shop_stat 发货状态 1未发货 2 已发货
 * @property string $numbers 快递单号
 * @property string $expre 快递公司
 * @property string $create_at 创建时间
 * @property string $pay_at 支付时间
 * @property string $shop_at 发货时间
 * @property int $address_id 收货地址id
 */
class WeekOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'week_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order', 'user_id', 'address_id'], 'required'],
            [['user_id', 'num', 'order_id', 'pay_stat', 'shop_stat', 'address_id'], 'integer'],
            [['price', 'subtotal'], 'number'],
            [['create_at', 'pay_at', 'shop_at'], 'safe'],
            [['order'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['numbers'], 'string', 'max' => 45],
            [['expre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'user_id' => 'User ID',
            'price' => 'Price',
            'subtotal' => 'Subtotal',
            'num' => 'Num',
            'code' => 'Code',
            'order_id' => 'Order ID',
            'pay_stat' => 'Pay Stat',
            'shop_stat' => 'Shop Stat',
            'numbers' => 'Numbers',
            'expre' => 'Expre',
            'create_at' => 'Create At',
            'pay_at' => 'Pay At',
            'shop_at' => 'Shop At',
            'address_id' => 'Address ID',
        ];
    }
    /**
     * 用户领取礼包
     * */
    public function order($params)
    {
        $openid = Wechat::openid($params['openid']);
        $redis = yii::$app->redis;
        //查看是否领过这个礼包
        $libao = $redis->sismember('LIBAO::'.$openid->id,$params['id']);
        if($libao){
            throw new ErrorException('已经领取过此礼包');
        }
        //获取订单 要领取的id  $params['id']  SMEMBERS
        $order = $redis->smembers('ORDER:'.$openid->id);//通过获取的值查goods表
        $address = Address::findOne(['id'=>$params['address_id']]);
        if($address == false){
            throw new ErrorException('无效的收货地址');
        }
        if(!empty($params['code'])){
            $transaction = yii::$app->db->beginTransaction();
            try {
                $sql = "select id,stock,price from periphery where id = :id for update";
                $good = Yii::$app->db->createCommand($sql,[':id'=>$params['goods_id']])->queryOne();
//                        $nun = $good['stock'] - $params['num'];
                if($good) {
                    $ordercode = rand(1000, 9999) . date("YmdHis");
                    $model = yii::$app->db->createCommand()->insert(
                        'week_order',
                        [
                            'order'  => $ordercode,
                            'user_id'  => $openid->id,
                            'address_id'  => $address->id,
                            'order_id'  => $good['id'],
                            'create_at'   =>date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if($model == true){
                        $last = yii::$app->db->lastInsertID;
                        $transaction->commit();
                        $redis->sadd('LIBAO::'.$openid->id,$good['id']);
                        $da = (new\yii\db\Query())->select('a.id,a.order,a.user_id,a.order_id,a.create_at,a.address_id,
                        b.id as bid,b.name,b.image,c.id as cid,c.url')
                            ->from('week_order as a,periphery as b,oss as c')
                            ->where('a.id=:id',['id'=>$last])
                            ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                            ->andWhere('a.order_id=b.id')
                            ->andWhere('b.image=c.id')
                            ->one();
                        return $da;//934506399
                    }
                }
            }catch (\Throwable $e){
                $transaction->rollBack();
                throw $e;
            }
        }
        //购买的时候先判断有没有资格领取 再判断领取过没有 都成立就购买
        if($order){
            //领取的礼包id
            $goods = Periphery::findOne(['id'=>$params['id']]);
//            return $goods;
            //
            if(in_array($goods['style_id'],$order)){
                $transaction = yii::$app->db->beginTransaction();
                try {
                        $sql = "select id from periphery where id = :id for update";
                        $good = Yii::$app->db->createCommand($sql,[':id'=>$params['id']])->queryOne();
//                        $nun = $good['stock'] - $params['num'];
                        if($good) {
                            $ordercode = rand(1000, 9999) . date("YmdHis");
                            $model = yii::$app->db->createCommand()->insert(
                                'week_order',
                                [
                                    'order'  => $ordercode,
                                    'user_id'  => $openid->id,
                                    'address_id'  => $address->id,
                                    'order_id'  => $good['id'],
                                    'create_at'   =>date('Y-m-d H:i:s'),
                                ]
                            )->execute();
                            if($model == true){
                                $last = yii::$app->db->lastInsertID;
                                $transaction->commit();
                                $redis->sadd('LIBAO::'.$openid->id,$good['id']);
                                $da = (new\yii\db\Query())->select('a.id,a.order,a.user_id,a.order_id,a.create_at,a.address_id,
                        b.id as bid,b.name,b.image,c.id as cid,c.url')
                                    ->from('week_order as a,periphery as b,oss as c')
                                    ->where('a.id=:id',['id'=>$last])
                                    ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                                    ->andWhere('a.order_id=b.id')
                                    ->andWhere('b.image=c.id')
                                    ->one();;
                                return $da;
                            }
                        }
                    }catch (\Throwable $e){
                        $transaction->rollBack();
                        throw $e;
                    }
            }else{
                throw new ErrorException('没有领取资格');
            }
        }else{
            throw new ErrorException('没有领取资格');
        }

    }

    /**
     * 礼包详情
     * */
    public function details($params)
    {
        $openid = Wechat::openid($params['openid']);
        $da = (new\yii\db\Query())->select('a.id,a.order,a.user_id,a.order_id,a.create_at,a.address_id,
                        b.id as bid,b.name,b.image,b.details,c.id as cid,c.url')
            ->from('week_order as a,periphery as b,oss as c')
            ->where('a.id=:id',['id'=>$params['id']])
            ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('a.order_id=b.id')
            ->andWhere('b.image=c.id')
            ->one();
        $arr = explode(',',$da['details']);
        foreach ($arr as $key=>$value){
            $oss[] = Oss::findOne(['id'=>$value]);
        }
        return['items'=>$da,
                'image'=>$oss
            ];

    }
    /**
     *
     * 列表
     * */
    public function show($params)
    {
        $pagesize = !empty($params['size'])?$params['size']:10;
        $openid = Wechat::openid($params['openid']);
        $data = (new\yii\db\Query())->select('a.id,a.order,a.user_id,a.order_id,a.create_at,a.address_id,
                        b.id as bid,b.name,b.image,c.id as cid,c.url')
            ->from('week_order as a,periphery as b,oss as c')
            ->where('a.user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('a.order_id=b.id')
            ->andWhere('b.image=c.id');
        $pages = new Pagination(['totalCount'=>$data->count(),'pageSize'=>$pagesize]);
        $model = $data->offset($pages->offset)->limit($pages->limit);
        return[
          'items'=>$model,
          'pages'=>$pages
        ];
    }
}
