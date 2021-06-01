<?php

namespace api\modules\v1\models;

use backend\modules\v1\models\Config;
use common\base\ErrorException;
use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "game_order".
 *
 * @property int $id 自增
 * @property string $order_code 订单号
 * @property int $user_id 用户id
 * @property int $goods_id 商品id
 * @property string $price 商品单价
 * @property string $subtotal 商品总价
 * @property int $address_id 收货地址信息
 * @property int $num 商品数量
 * @property int $shop_state 1 未发货 2 待收货 3
 * @property int $pay_state 1 未支付 2 已支付 3 已退款
 * @property string $expre 快递公司id
 * @property string $numbers 快递单号
 * @property int $refun 1用户发起退货申请 2 管理员同意退货
 * @property string $create_at 创建时间
 * @property string $pay_at 支付时间
 * @property string $shop_at 发货时间
 * @property string $run_at 退款时间
 */
class GameOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_code', 'user_id', 'goods_id', 'price', 'subtotal', 'address_id', 'num', 'shop_state', 'pay_state'], 'required'],
            [['user_id', 'goods_id', 'address_id', 'num', 'shop_state', 'pay_state', 'refun'], 'integer'],
            [['price', 'subtotal'], 'number'],
            [['create_at', 'pay_at', 'shop_at', 'run_at'], 'safe'],
            [['order_code'], 'string', 'max' => 45],
            [['expre'], 'string', 'max' => 100],
            [['numbers'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_code' => 'Order Code',
            'user_id' => 'User ID',
            'goods_id' => 'Goods ID',
            'price' => 'Price',
            'subtotal' => 'Subtotal',
            'address_id' => 'Address ID',
            'num' => 'Num',
            'shop_state' => 'Shop State',
            'pay_state' => 'Pay State',
            'expre' => 'Expre',
            'numbers' => 'Numbers',
            'refun' => 'Refun',
            'create_at' => 'Create At',
            'pay_at' => 'Pay At',
            'shop_at' => 'Shop At',
            'run_at' => 'Run At',
        ];
    }
    /**
     * 不知道干嘛
     * */
    public function order($params)
    {
        $openid = Wechat::openid($params['openid']);
        $redis = yii::$app->redis;
        $go = Goods::findOne(['id'=>$params['goods_id']]);
        //测试完成之后打开这个
        $max = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,a.duihuan,b.styimage_id,max(b.lerver)')
            ->from('integral as a')
            ->innerJoin('card b','b.id=a.card_id')
            ->where('a.user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('b.lerver=5')
            ->andWhere('b.styimage_id=:styimage_id',['styimage_id'=>$go->id])
            ->groupBy('b.styimage_id')
            ->count();
        $ore = $redis->get('NUM:'.$openid->id);
        if($ore > $go->frequency){
            throw new ErrorException('购买次数超限');
        }
        if($max < $go->style_num){
            $nfc = (new \yii\db\Query())->select('userid,id')
                ->from('number')
                ->where('userid=:userid',['userid'=>$openid->id])
                ->count();
            if($nfc < $go->nfc){
                throw new ErrorException('目前没有资格购买');
            }else{
                $x = $redis->get('NUM:'.$openid->id);
                if($x > $go->frequency){
                    throw new ErrorException('目前每个用户允许购买:'.$go->frequency.'此');
                }
                if($go->buynum < $params['num']){
                    throw new ErrorException('购买数量超过了最大购买数,建议减少购买数量');
                }
                $address = Address::findOne(['id'=>$params['address_id']]);
                if($address == false){
                    throw new ErrorException('无效的收货地址');
                }

                $transaction = yii::$app->db->beginTransaction();
                try {
                    $sql = "select id,stock,price from goods where id = :id for update";
                    $good = Yii::$app->db->createCommand($sql,[':id'=>$params['goods_id']])->queryOne();
                    $nun = $good['stock'] - $params['num'];
                    if($nun > 0){
                        $ordercode = rand(1000, 9999) .  date("YmdHis");
                        $model = yii::$app->db->createCommand()->insert(
                            'game_order',
                            [
                                'user_id'=>$openid->id,
                                'goods_id'=>$good['id'],
                                'price'=>$good['price'],
                                'address_id'=>$address->id,
                                'order_code'=>$ordercode,
                                'num'=>$params['num'],
                                'subtotal'=>$params['num']*$good['price'],
                                'shop_state'=>1,
                                'pay_state'=>1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($model == true){
                            $last = yii::$app->db->lastInsertID;
                            $data = yii::$app->db->createCommand()->update(
                                'goods',
                                [
                                    'stock'=> $nun,
                                    'update_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'id'=>$good['id']
                                ]
                            )->execute();
                            if($data == true){
                                $transaction->commit();
                                $redis->sadd('ORDER:'.$openid->id,$good['id']);
                                $redis->incr('NUM:'.$openid->id);
                                $da = self::findOne(['id'=>$last]);
                                return $da;
                            }
                        }
                    }else{
                        throw new ErrorException('此商品的库存不足');
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }else{
            $x = $redis->get('NUM:'.$openid->id);
            if($x > $go->frequency){
                throw new ErrorException('目前每个用户允许购买:'.$go->frequency.'此');
            }
            if($go->buynum < $params['num']){
                throw new ErrorException('购买数量超过了最大购买数,建议减少购买数量');
            }
            $address = Address::findOne(['id'=>$params['address_id']]);
            if($address == false){
                throw new ErrorException('无效的收货地址');
            }

            $transaction = yii::$app->db->beginTransaction();
            try {
                $sql = "select id,stock,price from goods where id = :id for update";
                $good = Yii::$app->db->createCommand($sql,[':id'=>$params['goods_id']])->queryOne();
                $nun = $good['stock'] - $params['num'];
                if($nun > 0){
                    $ordercode = rand(1000, 9999) .  date("YmdHis");
                    $model = yii::$app->db->createCommand()->insert(
                        'game_order',
                        [
                            'user_id'=>$openid->id,
                            'goods_id'=>$good['id'],
                            'price'=>$good['price'],
                            'address_id'=>$address->id,
                            'order_code'=>$ordercode,
                            'num'=>$params['num'],
                            'subtotal'=>$params['num']*$good['price'],
                            'shop_state'=>1,
                            'pay_state'=>1,
                            'create_at'=>date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if($model == true){
                        $last = yii::$app->db->lastInsertID;
                        $data = yii::$app->db->createCommand()->update(
                            'goods',
                            [
                                'stock'=> $nun,
                                'update_at' => date('Y-m-d H:i:s')
                            ],
                            [
                                'id'=>$good['id']
                            ]
                        )->execute();
                        if($data == true){
                            $transaction->commit();
                            $redis->sadd('ORDER:'.$openid->id,$good['id']);
                            $redis->incr('NUM:'.$openid->id);
                            $da = self::findOne(['id'=>$last]);
                            return $da;
                        }
                    }
                }else{
                    throw new ErrorException('此商品的库存不足');
                }
            }catch (\Throwable $e){
                $transaction->rollBack();
                throw $e;
            }
        }


    }
    /**
     * 订单列表
     * */
    public function show($params)
    {
        $openid = Wechat::openid($params['openid']);
        $pagesize = !empty($params['size'])?$params['size']:10;
        $model = (new \yii\db\Query())->select('a.id,a.order_code,a.user_id,a.goods_id,a.price,a.subtotal,a.address_id,
            a.num,a.shop_state,a.pay_state,a.create_at,a.pay_at,a.shop_at,a.run_at,
            b.id as bid,b.goods_name,b.image,c.id as cid,c.url')
            ->from('game_order as a,goods as b,oss as c')
            ->where('a.goods_id=b.id')
            ->andWhere('b.image=c.id')
            ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id]);
        $pages = new Pagination(['totalCount'=>$model->count(),'pageSize'=>$pagesize]);
        $data = $model->offset($pages->offset)->limit($pages->limit)->all();
        return ['items'=>$data,'pages'=>$pages];
    }
    /**
     * 详情
     *
     * */
    public function details($params)
    {
        $openid = Wechat::openid($params['openid']);
        $model = (new \yii\db\Query())->select('a.id,a.order_code,a.user_id,a.goods_id,a.price,a.subtotal,a.address_id,
            a.num,a.shop_state,a.pay_state,a.create_at,a.pay_at,a.shop_at,a.run_at,a.expre,a.numbers,
            b.id as bid,b.goods_name,b.image,c.id as cid,c.url,
            e.id as eid,e.user_id,e.province,e.city,e.district,e.mobile,e.address,e.name')
            ->from('game_order as a,goods as b,oss as c,address as e')
            ->where('a.goods_id=b.id')
            ->andWhere('b.image=c.id')
            ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('a.address_id=e.id')
            ->andWhere('a.id=:id',['id'=>$params['id']])
            ->one();
        if($model == false){
            throw new ErrorException('无效的订单id');
        }
        return $model;
    }
    /**
     * 删除订单
     * */
    public function strick($params)
    {
        $openid = Wechat::openid($params['openid']);
        $model = GameOrder::findOne(['id'=>$params['id'],'user_id'=>$openid->id]);
        if($model == false){
            throw new ErrorException('无效的订单id');
        }
        if($model->shop_state == 2){
            throw new ErrorException('此订单已发货无法删除');
        }
        if($model->pay_state == 2){
            throw new ErrorException('此订单已支付无法删除');
        }
        $transaction = yii::$app->db->beginTransaction();
        try {
            $data = yii::$app->db->createCommand()->delete(
                'game_order',
                [
                    'id'=>$model->id
                ]
            )->execute();
            if($data == true){
                $sql = "select id,stock from goods where id = :id for update";
                $good = Yii::$app->db->createCommand($sql,[':id'=>$model->goods_id])->queryOne();
                $nun = $good['stock'] + $model->num;
                $goods = yii::$app->db->createCommand()->update(
                    'goods',
                    [
                        'stock'=>$nun
                    ],
                    [
                        'id'=>$good['id']
                    ]
                )->execute();
                if($goods == true){
                    $transaction->commit();
                    return '删除订单成功';
                }
            }
        }catch(\Throwable $e){
            $transaction->rollBack();
            throw $e;
        }


    }
    /**
     * 快递列表
     * */
    public function express($param)
    {
        //参数设置
        $key = 'UNtvdnzK4066';    //客户授权key
        $customer = 'E411AA008996EBC73ADF4980B0E91A98';     //查询公司编号
//        $param = array (
//            'com' => 'yunda',			//快递公司编码
//            'num' => 'SF1885646313140',	//快递单号
//        );
//        $da = (new \yii\db\Query())->select('')
//            ->from('')
//            ->where('')
//            ->one();
        //请求参数
        $post_data = array();
        $post_data["customer"] = $customer;
        $post_data["param"] = json_encode($param);
        $sign = md5($post_data["param"] . $key . $post_data["customer"]);
        $post_data["sign"] = strtoupper($sign);
        $url = 'http://poll.kuaidi100.com/poll/query.do';    //实时查询请求地址
        $param = "";
        foreach ($post_data as $k => $v) {
            $param .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
        }
        $arams = [
            'ia'=>'dasehdabuzhidao',

        ];
        $post_data = substr($param, 0, -1);
        //发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\"", '"', $result);
        $data = json_decode($data);
        if ($data->message == 'ok'){
            $model = (new \yii\db\Query())->select('numbers,com')
                ->from('expre')
                ->where(['numbers' => $data->com])
                ->one();
            $data->com = $model['com'];
            return array($data);
        }
        if ($data->result == false){
            return $data->message;
        }
    }
    /**
     * 发起退款申请
     * */
    public function refund($params)
    {
        Wechat::openid($params['openid']);
        $order = self::findOne(['id'=>$params['id']]);
        if($order == false){
            throw new ErrorException('无效的订单id');
        }
        if($order->pay_state == 2){
            $model = yii::$app->db->createCommand()->update(
                'game_order',
                [
                    'refun'=>'1'
                ],
                [
                    'id'=>$order->id
                ]
            )->execute();
            if($model == true){
                return '审核退款成功';
            }else{
                throw new ErrorException('审核退款失败');
            }
        }else{
            throw new ErrorException('此订单已退款或者未支付');
        }
    }

}
