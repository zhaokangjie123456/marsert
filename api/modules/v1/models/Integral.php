<?php

namespace api\modules\v1\models;

use backend\modules\v1\models\Card;
use common\base\ErrorException;
use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "integral".
 *
 * @property int $id 自增
 * @property int $integral 积分数量
 * @property int $user_id 用户id
 * @property int $card_id 星级卡id
 * @property string $create_at 签到时间
 * @property int $state 1获取积分 2消费积分 3分享朋友圈 4 发送给朋友 5 NFC兑换的积分
 * @property int $duihuan 1 兑换了手办 无法使用2次
 * @property string $update_at 更新时间
 */
class Integral extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integral';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'state'], 'required'],
            [['id',  'user_id', 'card_id', 'state', 'duihuan'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['user_id'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'integral' => 'Integral',
            'user_id' => 'User ID',
            'card_id' => 'Card ID',
            'create_at' => 'Create At',
            'state' => 'State',
            'duihuan' => 'Duihuan',
            'update_at' => 'Update At',
        ];
    }
    public function getDbConnection()
    {

        return Yii::app()->db1;
    }
    /**
     * 签到
     * */
    public function singin($params)
    {
//        $openid = Wechat::openid($params);
//        $star = date('Y-m-d');
//        $st = $star.' '.'00:00:00';
//        $end = $star.' '.'23:59:59';
//        $model = (new \yii\db\Query())->select('user_id,create_at,state,card_id')
//            ->from('integral')
//            ->where('user_id=:user_id',['user_id'=>$openid->id])
//            ->andWhere('state=1')
//            ->andFilterWhere(['>=','create_at',$st])
//            ->andFilterWhere(['<=','create_at',$end])
//            ->all();
//        //当天没有签到
//        if (empty($model)) {
//            $singin = Integral::findAll(['user_id'=>$openid->id,'state'=>1]);
//            $num = Number::findAll(['userid'=>$openid->id]);
//            $card = Card::findAll(['lerver'=>2]);
//            $max =$card[mt_rand(0, count($card) - 1)];
//            $path = 'http://gbackend.youjingxi.net.cn/uploads/';
//            if(empty($singin)){
//                $data = yii::$app->db->createCommand()->insert(
//                    'integral',
//                    [
//                    'user_id'=>$openid->id,
//                    'card_id'=>$max['id'],
//                    'state'=>1,
//                    'upgrade'=>1,
//                    'create_at'=>date('Y-m-d H:i:s'),
//                    ]
//                )->execute();
//                if( $data== true){
//                    $last = yii::$app->db->lastInsertID;
//                    $hao = (new \yii\db\Query())->select('a.id,a.card_id,b.id as bid,b.styimage_id,b.lerver')
//                        ->from('integral as a,card as b')
//                        ->where('a.id=:id',['id'=>$last])
//                        ->andWhere('a.card_id=b.id')
//                        ->one();
//                    $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
//                        ->from('card')
//                        ->where('styimage_id=:styimage_id',['styimage_id'=>$hao['styimage_id']])
//                        ->andFilterWhere(['<','lerver',$hao['lerver']])
//                        ->all();
//                    foreach ($en as $key=>$value){
//                        $dael[] = yii::$app->db->createCommand()->insert(
//                            'integral',
//                            [
//                                'user_id'=>$openid->id,
//                                'card_id'=>$value['id'],
//                                'state'=>6,
//                                'upgrade'=>1,
//                                'create_at'=>date('Y-m-d H:i:s'),
//                            ]
//                        )->execute();
//                    }
//                    if($dael == true){
//                        $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
//                    b.id as bid,b.url')
//                            ->from('card as a,oss as b')
//                            ->where('a.cover_id=b.id')
//                            ->andWhere('a.id=:id',['id'=>$max['id']])
//                            ->one();
//                        return [
//                            'state'=>1,
//                            'integral'=>0,
//                            'picurl'=>$path.$rest['url']
//                        ];
//                    }else{
//                        throw new ErrorException('签到失败8');
//                    }
//                }else{
//                    throw new ErrorException('签到失败8');
//                }
//            }
//            $count = count($singin);
//            $car = Card::findAll(['lerver'=>3]);
//            $max =$car[mt_rand(0, count($car) - 1)];
//            if($count == '2') {
//                $nu = (new \yii\db\Query())->select('a.user_id,a.card_id,b.id,b.lerver')
//                    ->from('integral as a,card as b')
//                    ->where('a.userid=:userid', ['userid' => $openid->id])
//                    ->andWhere('a.card_id=b.id')
//                    ->andWhere('b.lerver=3')
//                    ->one();
//                if ($nu) {
//                    $data = yii::$app->db->createCommand()->insert(
//                        'integral',
//                        [
//                            'user_id' => $openid->id,
//                            'integral' => 300,
//                            'state' => 1,
//                            'create_at' => date('Y-m-d H:i:s'),
//                        ]
//                    )->execute();
//                    if ($data == true) {
//                        return [
//                            'state' => 2,
//                            'integral' => 300,
//                            'picurl' => 0
//                        ];
//                    } else {
//                        throw new ErrorException('签到失败1');
//                    }
//                } else {
//                    $data = yii::$app->db->createCommand()->insert(
//                        'integral',
//                        [
//                            'user_id' => $openid->id,
//                            'card_id' => $max['id'],
//                            'state' => 1,
//                            'upgrade' => 1,
//                            'create_at' => date('Y-m-d H:i:s'),
//                        ]
//                    )->execute();
//                    if ($data == true) {
//                        $last = yii::$app->db->lastInsertID;
//                        $hao = (new \yii\db\Query())->select('a.id,a.card_id,b.id as bid,b.styimage_id,b.lerver')
//                            ->from('integral as a,card as b')
//                            ->where('a.id=:id', ['id' => $last])
//                            ->andWhere('a.card_id=b.id')
//                            ->one();
//                        $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
//                            ->from('card')
//                            ->where('styimage_id=:styimage_id', ['styimage_id' => $hao['styimage_id']])
//                            ->andFilterWhere(['<', 'lerver', $hao['lerver']])
//                            ->all();
//                        foreach ($en as $key => $value) {
//                            $dael[] = yii::$app->db->createCommand()->insert(
//                                'integral',
//                                [
//                                    'user_id' => $openid->id,
//                                    'card_id' => $value['id'],
//                                    'state' => 6,
//                                    'upgrade' => 1,
//                                    'create_at' => date('Y-m-d H:i:s'),
//                                ]
//                            )->execute();
//                        }
//                        if ($dael == true) {
//                            $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
//                    b.id as bid,b.url')
//                                ->from('card as a,oss as b')
//                                ->where('a.cover_id=b.id')
//                                ->andWhere('a.id=:id', ['id' => $max['id']])
//                                ->one();
//                            return [
//                                'state' => 1,
//                                'integral' => 0,
//                                'picurl' => $path . $rest['url']
//                            ];
//                        } else {
//                            throw new ErrorException('签到失败2');
//                        }
//                    }
//                }
//            }
//                if ($count == '6') {
//                    $n = (new \yii\db\Query())->select('a.userid,a.card_id,b.id,b.lerver')
//                        ->from('number as a,card as b')
//                        ->where('a.userid=:userid', ['userid' => $openid->id])
//                        ->andWhere('a.card_id=b.id')
//                        ->andWhere('b.lerver=4')
//                        ->one();
//                    $car = Card::findAll(['lerver' => 4]);
//                    $ma = $car[mt_rand(0, count($car) - 1)];
//                    if ($n) {
//                        $data = yii::$app->db->createCommand()->insert(
//                            'integral',
//                            [
//                                'user_id' => $openid->id,
//                                'integral' => 500,
//                                'state' => 1,
//                                'create_at' => date('Y-m-d H:i:s'),
//                            ]
//                        )->execute();
//                        if ($data == true) {
//                            return [
//                                'state' => 2,
//                                'integral' => 500,
//                                'picurl' => 0
//                            ];
//                        } else {
//                            throw new ErrorException('签到失败3');
//                        }
//                    } else {
//                        $data = yii::$app->db->createCommand()->insert(
//                            'integral',
//                            [
//                                'user_id' => $openid->id,
//                                'card_id' => $ma['id'],
//                                'state' => 1,
//                                'upgrade' => 1,
//                                'create_at' => date('Y-m-d H:i:s'),
//                            ]
//                        )->execute();
//                        if ($data == true) {
//                            $last = yii::$app->db->lastInsertID;
//                            $hao = (new \yii\db\Query())->select('a.id,a.card_id,b.id as bid,b.styimage_id,b.lerver')
//                                ->from('integral as a,card as b')
//                                ->where('a.id=:id', ['id' => $last])
//                                ->andWhere('a.card_id=b.id')
//                                ->one();
//                            $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
//                                ->from('card')
//                                ->where('styimage_id=:styimage_id', ['styimage_id' => $hao['styimage_id']])
//                                ->andFilterWhere(['<', 'lerver', $hao['lerver']])
//                                ->all();
//                            foreach ($en as $key => $value) {
//                                $dael[] = yii::$app->db->createCommand()->insert(
//                                    'integral',
//                                    [
//                                        'user_id' => $openid->id,
//                                        'card_id' => $value['id'],
//                                        'state' => 6,
//                                        'upgrade' => 1,
//                                        'create_at' => date('Y-m-d H:i:s'),
//                                    ]
//                                )->execute();
//                            }
//                            if ($dael == true) {
//                                $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
//                    b.id as bid,b.url')
//                                    ->from('card as a,oss as b')
//                                    ->where('a.cover_id=b.id')
//                                    ->andWhere('a.id=:id', ['id' => $max['id']])
//                                    ->one();
//                                return [
//                                    'state' => 1,
//                                    'integral' => 0,
//                                    'picurl' => $path . $rest['url']
//                                ];
//                            } else {
//                                throw new ErrorException('签到失败4');
//                            }
//                        }
//                    }
//                }
//                    if ($count != 2 || $count != 6) {
//                        $data = yii::$app->db->createCommand()->insert(
//                            'integral',
//                            [
//                                'user_id' => $openid->id,
//                                'integral' => 100,
//                                'state' => 1,
//                                'create_at' => date('Y-m-d H:i:s'),
//                            ]
//                        )->execute();
//                        if ($data == true) {
//                            return [
//                                'state' => 2,
//                                'integral' => 100,
//                                'picurl' => 0
//                            ];
//                        } else {
//                            throw new ErrorException('签到失败5');
//                        }
//                    }
//        }else{
//            throw new ErrorException('每天只能签到一次');
//        }


        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        //获取签到的天数
        $scard = $redis->scard('SIN::'.$openid->id);
        $star = date('Y-m-d');
        $st = $star.' '.'00:00:00';
        $end = $star.' '.'23:59:59';
        $model = (new \yii\db\Query())->select('user_id,create_at,state,card_id')
            ->from('integral')
            ->where('user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('state=1')
            ->andFilterWhere(['>=','create_at',$st])
            ->andFilterWhere(['<=','create_at',$end])
            ->all();
        //当天没有签到
        if (empty($model)) {
            //第一天签到
            if ($scard == 0) {
                $card = Card::findAll(['lerver' => 2]);
                $max = $card[mt_rand(0, count($card) - 1)];
                $path = 'http://gbackend.youjingxi.net.cn/uploads/';
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'user_id' => $openid->id,
                            'card_id' => $max['id'],
                            'state' => 1,
                            'upgrade' => 1,
                            'create_at' => date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if ($data == true) {
                        $redis->sadd('INTE::' . $openid->id, $max['id']);
                        $ens = (new \yii\db\Query())->select('id,styimage_id,lerver')
                            ->from('card')
                            ->where('id=:id', ['id' => $max['id']])
                            ->one();
                        $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
                            ->from('card')
                            ->where('styimage_id=:styimage_id', ['styimage_id' => $ens['styimage_id']])
                            ->andFilterWhere(['<', 'lerver', $ens['lerver']])
                            ->all();
                        foreach ($en as $key => $value) {
                            $inie = $redis->sismember('INTE::'.$openid->id, $value['id']);
                            if($inie == 0){
                                $dael[] = yii::$app->db->createCommand()->insert(
                                    'integral',
                                    [
                                        'user_id' => $openid->id,
                                        'card_id' => $value['id'],
                                        'state' => 6,
                                        'upgrade' => 1,
                                        'create_at' => date('Y-m-d H:i:s'),
                                    ]
                                )->execute();
                                $redis->sadd('INTE::'.$openid->id, $value['id']);
                                continue;
                            }
                        }
                        if ($dael == true) {
                            $transaction->commit();
                            $redis->sadd('SIN::'.$openid->id,1);
                            $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
                    b.id as bid,b.url')
                                ->from('card as a,oss as b')
                                ->where('a.cover_id=b.id')
                                ->andWhere('a.id=:id', ['id' => $max['id']])
                                ->one();
                            return [
                                'state' => 1,
                                'integral' => 0,
                                'picurl' => $path . $rest['url']
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
            //第三天签到
            if ($scard == 2) {
                $car = Card::findAll(['lerver' => 3]);
                $max = $car[mt_rand(0, count($car) - 1)];
                $nu = (new \yii\db\Query())->select('a.user_id,a.card_id,b.id,b.lerver')
                    ->from('integral as a,card as b')
                    ->where('a.user_id=:user_id', ['user_id' => $openid->id])
                    ->andWhere('a.card_id=b.id')
                    ->andWhere('b.lerver=3')
                    ->one();
                if ($nu) {
                    $data = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'user_id' => $openid->id,
                            'integral' => 300,
                            'state' => 1,
                            'create_at' => date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if ($data == true) {
                        $redis->sadd('SIN::'.$openid->id,3);
                        return [
                            'state' => 2,
                            'integral' => 300,
                            'picurl' => 0
                        ];
                    } else {
                        throw new ErrorException('签到失败1');
                    }
                } else {
                    $transaction = yii::$app->db->beginTransaction();
                    try {
                        $data = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'user_id' => $openid->id,
                                'card_id' => $max['id'],
                                'state' => 1,
                                'upgrade' => 1,
                                'create_at' => date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        $redis->sadd('INTE::' . $openid->id, $max['id']);
                        if ($data == true) {
                            $ens = (new \yii\db\Query())->select('id,styimage_id,lerver')
                                ->from('card')
                                ->where('id=:id', ['id' => $max['id']])
                                ->one();
                            $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
                                ->from('card')
                                ->where('styimage_id=:styimage_id', ['styimage_id' => $ens['styimage_id']])
                                ->andFilterWhere(['<', 'lerver', $ens['lerver']])
                                ->all();
                            foreach ($en as $key => $value) {
                                $inie = $redis->sismember('INTE::'.$openid->id, $value['id']);
                                if($inie == 0){
                                    $dael[] = yii::$app->db->createCommand()->insert(
                                        'integral',
                                        [
                                            'user_id' => $openid->id,
                                            'card_id' => $value['id'],
                                            'state' => 6,
                                            'upgrade' => 1,
                                            'create_at' => date('Y-m-d H:i:s'),
                                        ]
                                    )->execute();
                                    $redis->sadd('INTE::'.$openid->id, $value['id']);
                                    continue;
                                }
                            }
                            if ($dael == true) {
                                $transaction->commit();
                                $redis->sadd('SIN::'.$openid->id,3);
                                $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
                        b.id as bid,b.url')
                                    ->from('card as a,oss as b')
                                    ->where('a.cover_id=b.id')
                                    ->andWhere('a.id=:id', ['id' => $max['id']])
                                    ->one();
                                return [
                                    'state' => 1,
                                    'integral' => 0,
                                    'picurl' => $path . $rest['url']
                                ];
                            }
                        }
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                }
            }
            //第7天签到
            if ($scard == 6) {
                $car = Card::findAll(['lerver' => 4]);
                $max = $car[mt_rand(0, count($car) - 1)];
                $nu = (new \yii\db\Query())->select('a.user_id,a.card_id,b.id,b.lerver')
                    ->from('integral as a,card as b')
                    ->where('a.user_id=:user_id', ['user_id' => $openid->id])
                    ->andWhere('a.card_id=b.id')
                    ->andWhere('b.lerver=4')
                    ->one();
                if ($nu) {
                    $data = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'user_id' => $openid->id,
                            'integral' => 300,
                            'state' => 1,
                            'create_at' => date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if ($data == true) {
                        $redis->sadd('SIN::'.$openid->id,7);
                        return [
                            'state' => 2,
                            'integral' => 300,
                            'picurl' => 0
                        ];
                    } else {
                        throw new ErrorException('签到失败1');
                    }
                } else {
                    $transaction = yii::$app->db->beginTransaction();
                    try {
                        $data = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'user_id' => $openid->id,
                                'card_id' => $max['id'],
                                'state' => 1,
                                'upgrade' => 1,
                                'create_at' => date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        $redis->sadd('INTE::'.$openid->id, $max['id']);
                        if ($data == true) {
                            $ens = (new \yii\db\Query())->select('id,styimage_id,lerver')
                                ->from('card')
                                ->where('id=:id', ['id' => $max['id']])
                                ->one();
                            $en = (new \yii\db\Query())->select('id,styimage_id,lerver')
                                ->from('card')
                                ->where('styimage_id=:styimage_id', ['styimage_id' => $ens['styimage_id']])
                                ->andFilterWhere(['<', 'lerver', $ens['lerver']])
                                ->all();
                            foreach ($en as $key => $value) {
                                $inie = $redis->sismember('INTE::'.$openid->id, $value['id']);
                                if($inie == 0){
                                    $dael[] = yii::$app->db->createCommand()->insert(
                                        'integral',
                                        [
                                            'user_id' => $openid->id,
                                            'card_id' => $value['id'],
                                            'state' => 6,
                                            'upgrade' => 1,
                                            'create_at' => date('Y-m-d H:i:s'),
                                        ]
                                    )->execute();
                                    $redis->sadd('INTE::'.$openid->id, $value['id']);
                                    continue;
                                }
                            }
                            if ($dael == true) {
                                $transaction->commit();
                                $redis->sadd('SIN::'.$openid->id,7);
                                $rest = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,a.cover_id,
                                b.id as bid,b.url')
                                    ->from('card as a,oss as b')
                                    ->where('a.cover_id=b.id')
                                    ->andWhere('a.id=:id', ['id' => $max['id']])
                                    ->one();
                                return [
                                    'state' => 1,
                                    'integral' => 0,
                                    'picurl' => $path . $rest['url']
                                ];
                            }
                        }
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                }
            }
            //其他时间签到
            if ($scard != 2 || $scard != 6) {
                $data = yii::$app->db->createCommand()->insert(
                    'integral',
                    [
                        'user_id' => $openid->id,
                        'integral' => 100,
                        'state' => 1,
                        'create_at' => date('Y-m-d H:i:s'),
                    ]
                )->execute();
                if ($data == true) {
                    $redis->sadd('SIN::'.$openid->id,$scard+1);
                    return [
                        'state' => 2,
                        'integral' => 100,
                        'picurl' => 0
                    ];
                } else {
                    throw new ErrorException('签到失败5');
                }
            }
        }else{
            throw new ErrorException('今日已签到');
        }
    }
    /**
     * 兑换
     * */
    public function change($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        $id = $redis->get('DH::'.$openid->id);
        if($id == 1){
            throw new ErrorException('3秒之内只能兑换一次');
        }
        $inie = $redis->sismember('INTE::'.$openid->id, $params['card_id']);
        if($inie == 1){
            throw new ErrorException('已经拥有此卡册,无需再升级');
        }
        $cao =(new \yii\db\Query())->select('integral,user_id,card_id')
            ->from('integral as a')
            ->where('user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('card_id=:card_id',['card_id'=>$params['card_id']])
            ->all();
        if($cao){
            throw new ErrorException('已有相同等级的卡册');
        }
        //没有id  传的nid
        if(empty($params['id'])){
            $max = (new \yii\db\Query())->select('a.id,a.userid,a.upgrade,a.card_id,b.id as bid,b.lerver')
                ->from('number as a,card as b')
                ->where('a.userid=:userid',['userid'=>$openid->id])
                ->andWhere('a.card_id=b.id')
                ->andWhere('a.id=:id',['id'=>$params['nid']])
                ->one();
            if($max['upgrade'] == 2){
                throw new ErrorException('此卡册已升级，无需再次升级');
            }
            if($max['lerver'] == 5){
                throw new ErrorException('最高等级为5,可以升级其他卡片');
            }
            $dui = Card::findOne(['id'=>$params['card_id']]);
            $diff = $dui->lerver - $max['lerver'];
            if($diff > 1){
                throw new ErrorException('不能跳级开启');
            }
            $integral = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state!=2')
                ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
                ->andWhere(['!=','integral' ,' null'])
                ->all();
            $inte = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state=2')
                ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
                ->all();
            //没有消费
            if(empty($integral)){
                throw new ErrorException('目前还没有积分无法兑换');
            }
            if(empty($inte)){
                foreach ($integral as $value){
                    $data[] = $value['integral'];
                }
                //积分有多少
                $money = array_sum($data);
                $shengyu = $money - $dui->lerver *100;
                if($shengyu >= 0){
                    //消费
                    $model = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'integral'=> $dui->lerver*100,
                            'user_id'=> $openid->id,
                            'state'=> 2,
                            'create_at'=>date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if($model == true){
                        $redis->setex('DH::'.$openid->id,3,0);
                        $guoqi = $redis->incr('DH::'.$openid->id);
                        if($guoqi){
                            //得到新的
                            $data = yii::$app->db->createCommand()->insert(
                                'integral',
                                [
                                    'user_id'=> $openid->id,
                                    'card_id'=> $dui->id,
                                    'state'=> 6,
                                    'create_at'=>date('Y-m-d H:i:s'),
                                ]
                            )->execute();
                            if($data == true){
                                $redis->sadd('INTE::'.$openid->id, $dui->id);
                                $d = yii::$app->db->createCommand()->update(
                                    'number',
                                    [
                                        'upgrade'=> 2,
                                        'update_at'=>date('Y-m-d H:i:s'),
                                    ],
                                    [
                                        'id'=>$params['nid'],
                                    ]
                                )->execute();
                                if($d ==true){
                                    $oss = (new \yii\db\Query())->select('id,url')
                                        ->from('oss')
                                        ->where('id=:id',['id'=>$dui->cover_id])
                                        ->one();
                                    return [
                                        'state'=>1,
                                        'integral'=>0,
                                        'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$oss['url'],
                                    ];
                                }else{
                                    throw new ErrorException('兑换失败');
                                }
                            }else{
                                throw new ErrorException('兑换失败');
                            }
                        }else{
                            throw new ErrorException('兑换失败');
                        }
                    }else{
                        throw new ErrorException('兑换失败');
                    }
                }else{
                    throw new ErrorException('积分不足');
                }
            }
            if(empty($integral) || empty($inte)){
                throw new ErrorException('目前还没有积分无法兑换');
            }
            foreach ($integral as $value){
                $data[] = $value['integral'];
            }
            foreach ($inte as $value){
                $dat[] = $value['integral'];
            }
            //积分有多少
            $money = array_sum($data);
            //兑换有多少
            $change = array_sum($dat);
            $subtotal = $money -$change;
            $shengyu = $subtotal - $dui->lerver *100;
            if($shengyu >= 0){
                $model = yii::$app->db->createCommand()->insert(
                    'integral',
                    [
                        'integral'=> $dui->lerver*100,
                        'user_id'=> $openid->id,
                        'state'=> 2,
                        'create_at'=>date('Y-m-d H:i:s'),
                    ]
                )->execute();
                if($model == true){
                    $redis->setex('DH::'.$openid->id,3,0);
                    $guoqi = $redis->incr('DH::'.$openid->id);
                    if($guoqi){
                        $data = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'user_id'=> $openid->id,
                                'card_id'=> $dui->id,
                                'state'=> 6,
                                'upgrade'=> 1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($data == true){
                            $redis->sadd('INTE::'.$openid->id, $dui->id);
                            $d = yii::$app->db->createCommand()->update(
                                'integral',
                                [
                                    'upgrade'=> 2,
                                    'update_at'=>date('Y-m-d H:i:s'),
                                ],
                                [
                                    'id'=>$params['id'],
                                ]
                            )->execute();
                            if($d ==true){
                                $oss = (new \yii\db\Query())->select('id,url')
                                    ->from('oss')
                                    ->where('id=:id',['id'=>$dui->cover_id])
                                    ->one();
                                return [
                                    'state'=>1,
                                    'integral'=>0,
                                    'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$oss['url'],
                                ];
                            }else{
                                throw new ErrorException('兑换失败');
                            }
                        }else{
                            throw new ErrorException('兑换失败');
                        }
                    }else{
                        throw new ErrorException('兑换失败');
                    }
                }else{
                    throw new ErrorException('兑换失败');
                }
            }else{
                throw new ErrorException('积分不足');
            }
        }else{
            //传的id
            $max = (new \yii\db\Query())->select('a.id,a.upgrade,a.user_id,a.card_id,b.id as bid,b.lerver')
                ->from('integral as a,card as b')
                ->where('a.user_id=:user_id',['user_id'=>$openid->id])
                ->andWhere('a.card_id=b.id')
                ->andWhere('a.id=:id',['id'=>$params['id']])
                ->one();
            if($max['upgrade'] == 2){
                throw new ErrorException('此卡册已升级，无需再次升级');
            }
            if($max['lerver'] == 5){
                throw new ErrorException('最高等级为5,可以升级其他卡片');
            }
            $dui = Card::findOne(['id'=>$params['card_id']]);
            $diff = $dui->lerver - $max['lerver'];
            if($diff > 1){
                throw new ErrorException('不能跳级开启');
            }
            $integral = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state!=2')
                ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
                ->andWhere(['!=','integral' ,' null'])
                ->all();
            $inte = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state=2')
                ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
                ->all();
            //没有消费
            if(empty($integral)){
                throw new ErrorException('目前还没有积分无法兑换');
            }
            if(empty($inte)){
                foreach ($integral as $value){
                    $data[] = $value['integral'];
                }
                //积分有多少
                $money = array_sum($data);
                $shengyu = $money - $dui->lerver *100;
                if($shengyu >= 0){
                    $model = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'integral'=> $dui->lerver*100,
                            'user_id'=> $openid->id,
                            'state'=> 2,
                            'create_at'=>date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if($model == true){
                        $redis->setex('DH::'.$openid->id,3,0);
                        $guoqi = $redis->incr('DH::'.$openid->id);
                        if($guoqi){
                            $data = yii::$app->db->createCommand()->insert(
                                'integral',
                                [
                                    'user_id'=> $openid->id,
                                    'card_id'=> $dui->id,
                                    'state'=> 6,
                                    'create_at'=>date('Y-m-d H:i:s'),
                                ]
                            )->execute();
                            $redis->sadd('INTE::'.$openid->id, $dui->id);
                            if($data == true){
                                $oss = (new \yii\db\Query())->select('id,url')
                                    ->from('oss')
                                    ->where('id=:id',['id'=>$dui->cover_id])
                                    ->one();
                                return [
                                    'state'=>1,
                                    'integral'=>0,
                                    'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$oss['url'],
                                ];
                            }else{
                                throw new ErrorException('兑换失败');
                            }
                        }else{
                            throw new ErrorException('兑换失败');
                        }
                    }else{
                        throw new ErrorException('兑换失败');
                    }
                }else{
                    throw new ErrorException('积分不足');
                }
            }
            if(empty($integral) || empty($inte)){
                throw new ErrorException('目前还没有积分无法兑换');
            }
            foreach ($integral as $value){
                $data[] = $value['integral'];
            }
            foreach ($inte as $value){
                $dat[] = $value['integral'];
            }
            //积分有多少
            $money = array_sum($data);
            //兑换有多少
            $change = array_sum($dat);
            $subtotal = $money -$change;
            $shengyu = $subtotal - $dui->lerver *100;
            if($shengyu >= 0){
                $model = yii::$app->db->createCommand()->insert(
                    'integral',
                    [
                        'integral'=> $dui->lerver*100,
                        'user_id'=> $openid->id,
                        'state'=> 2,
                        'create_at'=>date('Y-m-d H:i:s'),
                    ]
                )->execute();
                if($model == true){
                    $redis->setex('DH::'.$openid->id,3,0);
                    $guoqi = $redis->incr('DH::'.$openid->id);
                    if($guoqi){
                        $data = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'user_id'=> $openid->id,
                                'card_id'=> $dui->id,
                                'state'=> 6,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        $redis->sadd('INTE::'.$openid->id, $dui->id);
                        if($data == true){
                            $oss = (new \yii\db\Query())->select('id,url')
                                ->from('oss')
                                ->where('id=:id',['id'=>$dui->cover_id])
                                ->one();
                            return [
                                'state'=>1,
                                'integral'=>0,
                                'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$oss['url'],
                            ];
                        }else{
                            throw new ErrorException('兑换失败');
                        }
                    }else{
                        throw new ErrorException('兑换失败');
                    }
                }else{
                    throw new ErrorException('兑换失败');
                }
            }else{
                throw new ErrorException('积分不足');
            }
        }

    }
    /**
     * 有多少积分
     * */
    public function payment($params)
    {
        $openid = Wechat::openid($params);
        $integral = (new \yii\db\Query())->select('integral,user_id,state')
            ->from('integral')
            ->where('state!=2')
            ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
            ->all();
        $inte = (new \yii\db\Query())->select('integral,user_id,state')
            ->from('integral')
            ->where('state=2')
            ->andWhere('user_id=:user_id',['user_id'=>$openid->id])
            ->all();
        if(empty($integral) || empty($inte)){
            return'目前还没有积分';
        }
        foreach ($integral as $value){
            $data[] = $value['integral'];
        }
        foreach ($inte as $value){
            $dat[] = $value['integral'];
        }
        //积分有多少
        $money = array_sum($data);
        //兑换有多少
        $change = array_sum($dat);
        $subtotal = $money -$change;
        return ['subtotal'=>$subtotal,'userinfo'=>$openid];
    }
    /**
     * NFC识别兑换
     * */
    public function match($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        $a = base64_decode($params['num']);
        $s = strlen($a);
        $ni = substr($a, 3, $s);
//        $ni = $params['num'];
            $car = (new \yii\db\Query())->select('a.id,a.userid,a.style_id,a.num_num,
            b.styimage_id,b.id as bid,b.lerver,b.cover_id')
                ->from('number as a,card as b')
                ->where('a.num_num=:num_num',['num_num'=>$ni])
                ->andWhere('a.style_id=b.styimage_id')
                ->andWhere('b.lerver=3')
                ->one();
            if($car['userid']){
                throw new ErrorException('此手办已兑换');
            }
            $list = $redis->SISMEMBER('NFC::'.$openid->id,$car['style_id']);
            $inie = $redis->sismember('INTE::'.$openid->id, $car['bid']);
            if($inie == 0 || $list == 0){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->update(
                        'number',
                        [
                            'userid'=>$openid->id,
                            'card_id'=> $car['bid'],
                            'update_at'=>date('Y-m-d H:i:s')
                        ],
                        [
                            'id'=>$car['id'],
                        ])->execute();
                    $redis->sadd('INTE::'.$openid->id, $car['bid']);
                    if($data == true){
                        $da = (new \yii\db\Query())->select('id,styimage_id,lerver')
                            ->from('card')
                            ->where('styimage_id=:styimage_id',['styimage_id'=>$car['styimage_id']])
                            ->all();
                        $rest = [];
                        foreach ($da as $key=>$value){
                            if($value['lerver'] < 3){
                                $inie = $redis->sismember('INTE::'.$openid->id, $value['id']);
//                                return $inie;
                                if($inie == 0) {
                                    $rest[] = yii::$app->db->createCommand()->insert(
                                        'integral',
                                        [
                                            'user_id' => $openid->id,
                                            'card_id' => $value['id'],
                                            'state' => 5,
                                            'upgrade' => 1,
                                            'create_at' => date('Y-m-d H:i:s'),
                                        ]
                                    )->execute();
                                    $redis->sadd('INTE::'.$openid->id, $value['id']);
                                    continue;
                                }
                            }
                        }
//                        var_dump($rest);
//                        return $rest;
                        if($rest == true){
                            $transaction->commit();
                            $oss = (new \yii\db\Query())->select('id,url')->from('oss')
                                ->where('id=:id',['id'=>$car['cover_id']])
                                ->one();
                            $r =(new \yii\db\Query())->select('a.id,a.image_id,a.type,b.id,b.name')
                                ->from('styleimage as a,image as b')
                                ->where('a.id=:id',['id'=>$car['styimage_id']])
                                ->andWhere('a.image_id=b.id')
                                ->one();
                            $redis->sadd('NFC::'.$openid->id,$car['style_id']);
                            return [
                                'state'=>1,
                                'integral'=>0,
                                'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$oss['url'],
                                'name'=>$r['type'].$r['name'],
                                'lerver'=>3
                            ];
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
            }else{
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $ni = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'integral'=>300,
                            'user_id'=>$openid->id,
                            'state'=>5,
                            'create_at'=>date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if($ni == true){
                        $d = yii::$app->db->createCommand()->update(
                            'number',
                            [
                                'userid'=>$openid->id,
                                'update_at'=>date('Y-m-d H:i:s')
                            ],
                            [
                                'id'=>$car['id'],
                            ])->execute();
                        if($d == true){
                            $transaction->commit();
                            return [
                                'state'=>2,
                                'integral'=>300,
                                'picurl'=>0
                            ];
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
            }
    }
    /**
     * 分享到朋友圈 1次
     * */
    public function share($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        $rest = $redis->get('FX'.'::'.$openid->id);
        if($rest == 5){
            throw new ErrorException('每天分享不能超过5次');
        }
        $array = [
            '0'=>40,
            '1'=>40,
//            '2'=>1400
        ];
        $rand = array_rand($array);
        $model = yii::$app->db->createCommand()->insert(
            'integral',
            [
                'integral'=>$array[$rand],
                'user_id'=>$openid->id,
                'state'=>3,
                'create_at'=>date('Y-m-d H:i:s'),
            ]
        )->execute();
        if($model == true){
            $start_time=strtotime(date("Y-m-d H:i:s"));
            //当天结束之间
            $end_time=date('Y-m-d',strtotime('+1 day'));
            $time = strtotime($end_time.' '.'00:00:00') ;
            //当前时间到明天凌晨还有多少秒
            $s = $time - $start_time;
            $value = !empty($rest)?$rest:0;
            $id = $redis->setex('FX'.'::'.$openid->id,$s,$value);
            $redis->incr('FX'.'::'.$openid->id);
            if($id == true){
                return $array[$rand];
            }
        }else{
            throw new ErrorException('分享失败');
        }
    }
    /**
     * 发送给朋友  5次
     * */
    public function friend($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;

        $rest = $redis->get('FS'.'::'.$openid->id);
        if($rest == 5){
            throw new ErrorException('每天只能分享给朋友5次');
        }
        $model = yii::$app->db->createCommand()->insert(
            'integral',
            [
                'integral'=>30,
                'user_id'=>$openid->id,
                'state'=>4,
                'create_at'=>date('Y-m-d H:i:s'),
            ]
        )->execute();
        if($model == true){
            $start_time=strtotime(date("Y-m-d H:i:s"));
            //当天结束之间
            $end_time=date('Y-m-d',strtotime('+1 day'));
            $time = strtotime($end_time.' '.'00:00:00') ;
            //当前时间到明天凌晨还有多少秒
            $s = $time - $start_time;
            $value = !empty($rest)?$rest:0;
            $id = $redis->setex('FS'.'::'.$openid->id,$s,$value);
            $redis->incr('FS'.'::'.$openid->id);
            if($id == true){
                return '30';
            }
        }else{
            throw new ErrorException('分享给朋友失败');
        }
    }
    /**
     * 集齐一套兑换
     * */
    public function whole($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        $id = $redis->get('WH::'.$openid->id);
        if(!$id){
            $array = explode(',',$params['id']);
            foreach ($array as $key=>$value){
                $integral[] = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,b.id as bid,b.lerver')
                    ->from('integral as a,card as b')
                    ->where('a.user_id=:user_id',['user_id'=>$openid->id])
                    ->andWhere('a.card_id=b.id')
                    ->andWhere('b.lerver=5')
                    ->andWhere('a.id=:id',['id'=>$value])
                    ->all();
            }
            $result = [];
            array_map(function ($value) use (&$result) {
                $result = array_merge($result, array_values($value));
            }, $integral);
            if(in_array([],$result)){
                throw new ErrorException('无效的参数');
            }
            $count = count($result);
            $arra = explode(',',$params['nid']);
            foreach ($arra as $key=>$valu){
                $num[] = (new \yii\db\Query())->select('a.id as nid,a.userid,a.card_id,b.id,b.lerver')
                    ->from('number as a,card as b')
                    ->where('a.userid=:userid',['userid'=>$openid->id])
                    ->andWhere('a.card_id=b.id')
                    ->andWhere('b.lerver=5')
                    ->andWhere('a.id=:id',['id'=>$valu])
                    ->all();
            }
            $resul = [];
            array_map(function ($value) use (&$resul) {
                $resul = array_merge($resul, array_values($value));
            }, $num);
            if(in_array([],$resul)){
                throw new ErrorException('无效的兑换id');
            }
            $co = count($resul);
            $total = $count+$co;
            if($total >= 7){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    foreach ($result as $key=>$value){
                        $data[] = yii::$app->db->createCommand()->update(
                            'integral',
                            [
                                'duihuan'=>1,
                                'update_at'=>date('Y-m-d H:i:s'),
                            ],
                        [
                            'id'=>$value['id']
                            ])->execute();
                    }
                    if($data == true){
                        foreach ($resul as $key=>$value){
                            $dat[] = yii::$app->db->createCommand()->update(
                                'number',
                                [
                                    'duihuan'=>1,
                                    'update_at'=>date('Y-m-d H:i:s'),
                                ],
                                [
                                    'id'=>$value['nid']
                                ])->execute();
                        }
                        if($dat == true){
                            $transaction->commit();
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }

            }else{
                throw new ErrorException('目前的星级卡为5级的数量不够');
            }
        }else{
            throw new ErrorException('30秒之内只能兑换一次');
        }

    }
    /**
     * 用户拥有的卡片
     * */
    public function details($params)
    {
        $openid = Wechat::openid($params);
        $intral = (new \yii\db\Query())->select('id,styimage_id,cover_id,lerver')
            ->from('card')
            ->where('id=:id',['id'=>$params['bid']])
            ->one();

        return $intral;
    }
    /**
     * 集齐一个5星卡兑换
     * */
    public function exchange($params)
    {

        $openid = Wechat::openid($params);
        if(!empty($params['id'])){
            $integral = (new \yii\db\Query())->select('a.id,a.user_id,a.duihuan,a.card_id,b.id as bid,b.lerver')
                ->from('integral as a,card as b')
                ->where('a.id=:id',['id'=>$params['id']])
                ->andWhere('a.card_id=b.id')
                ->andWhere('b.lerver=5')
                ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                ->one();
            $date = date('Y-m-d H:i:s');
            $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null and a.end_time>:end_time';
            $merchants = \Yii::$app->db1->createCommand($sql,[':end_time'=>$date])->queryAll();
            $rest = $merchants[mt_rand(0, count($merchants) -1)];
            if(empty($merchants)){
                throw new ErrorException('没有对应的优惠券请联系商家');
            }
            if($integral['duihuan'] == 1){
                throw new ErrorException('此卡卷已经兑换');
            }
            if($integral == true){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->update(
                        'integral',
                        [
                            'duihuan'=>1,
                        ],
                        [
                            'id'=>$integral['id']
                        ])->execute();
                    if($data == true){

//                        $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
//                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null';
//                        $merchants = \Yii::$app->db1->createCommand($sql)->queryAll();
//                        $rest = $merchants[mt_rand(0, count($merchants) - 1)];
                        $model = yii::$app->db->createCommand()->insert(
                            'coupon',
                            [
                                'code'=>$rest['code'],
                                'userid'=>$openid->id,
                                'integral_id'=>$params['id'],
                                'end_time'=>$merchants[0]['end_time'],
                                'price'=>$merchants[0]['price'],
                                'state'=>1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($model == true){
                            $m = Yii::$app->db1->createCommand()->update(
                                'code',
                                [
                                    'openid' =>1,
                                ],
                                [
                                    'code' => $rest['code'],
                                ])->execute();
                            if($m == true){
                                $transaction->commit();
                            }
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
                return $merchants[0]['price'];
            }else{
                throw new ErrorException('此卡卷没有到5级无法兑换');
            }
        }else{
            $integral = (new \yii\db\Query())->select('a.id,a.userid,a.duihuan,a.card_id,b.id as bid,b.lerver')
                ->from('number as a,card as b')
                ->where('a.id=:id',['id'=>$params['nid']])
                ->andWhere('a.card_id=b.id')
                ->andWhere('b.lerver=5')
                ->andWhere('a.userid=:userid',['userid'=>$openid->id])
                ->one();
            $date = date('Y-m-d H:i:s');
            $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null and a.end_time>:end_time';
            $merchants = \Yii::$app->db1->createCommand($sql,[':end_time'=>$date])->queryAll();
            $rest = $merchants[mt_rand(0, count($merchants) -1)];
            if(empty($merchants)){
                throw new ErrorException('没有对应的优惠券请联系商家');
            }
            if($integral['duihuan'] == 1){
                throw new ErrorException('此卡卷已经兑换');
            }
            if($integral == true){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->update(
                        'number',
                        [
                            'duihuan'=>1,
                        ],
                        [
                            'id'=>$integral['id']
                        ])->execute();
                    if($data == true){
//                        $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
//                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null';
//                        $merchants = \Yii::$app->db1->createCommand($sql)->queryAll();
//                        $rest = $merchants[mt_rand(0, count($merchants) - 1)];
                        $model = yii::$app->db->createCommand()->insert(
                            'coupon',
                            [
                                'code'=>$rest['code'],
                                'userid'=>$openid->id,
                                'number_id'=>$params['nid'],
                                'end_time'=>$merchants[0]['end_time'],
                                'price'=>$merchants[0]['price'],
                                'state'=>1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($model == true){
                            $m = Yii::$app->db1->createCommand()->update(
                                'code',
                                [
                                    'openid' =>1,
                                ],
                                [
                                    'code' => $rest['code'],
                                ])->execute();
                            if($m == true){
                                $transaction->commit();
                            }
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
                return $merchants[0]['price'];
            }else{
                throw new ErrorException('此卡卷没有到5级无法兑换');
            }
        }

    }
    /**
     * 优惠券列表
     * */
    public function show($params)
    {
        $openid = Wechat::openid($params);
        $pageSize = !empty($params['size'])?$params['size']:10;
        $data = (new \yii\db\Query())->select('code,userid,state,end_time,price')
            ->from('coupon')
            ->where('userid=:userid',['userid'=>$openid->id]);
        $pages = new Pagination(['totalCount'=>$data->count(),'pageSize'=>$pageSize]);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        if($model == false){
            return '目前没有优惠券';
        }
        return ['items'=>$model,'pages'=>$pages];
    }
    /**
     *卡卷详情
     * */
    public function detail($params)
    {
        $openid = Wechat::openid($params);
        $data = (new \yii\db\Query())->select('code,userid,state,end_time,price')
            ->from('coupon')
            ->where('userid=:userid',['userid'=>$openid->id])
            ->andWhere('code=:code',['code'=>$params['code']])
            ->one();
        if($data == false){
            throw new ErrorException('无效的code');
        }
        if($data['state'] == 2){
            throw new ErrorException('此优惠券已经使用过,目前无法查看');
        }
        return ['url'=>'https://admin.youjingxi.com.cn/code/'.$data['code'].'.'.'jpg'];
//        header("Content-Type: text/plain");
//        set_time_limit(0);
//        $infoString = "Hello World" . "\n";
//        while( isset($infoString) )
//        {
//            echo $infoString;
//            flush();
//            ob_flush();
//            sleep(5);
//        }
    }
    /**
     * 不着调
     * */
    public function buzhidao($params)
    {
        $openid = Wechat::openid($params);
        if(!empty($params['id'])){
            $integral = (new \yii\db\Query())->select('a.id,a.user_id,a.duihuan,a.card_id,b.id as bid,b.lerver')
                ->from('integral as a,card as b')
                ->where('a.id=:id',['id'=>$params['id']])
                ->andWhere('a.card_id=b.id')
                ->andWhere('b.lerver=5')
                ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                ->one();
            $date = date('Y-m-d H:i:s');
            $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null and a.end_time>:end_time';
            $merchants = \Yii::$app->db1->createCommand($sql,[':end_time'=>$date])->queryAll();
            $rest = $merchants[mt_rand(0, count($merchants) -1)];
            if(empty($merchants)){
                throw new ErrorException('没有对应的优惠券请联系商家');
            }
            if($integral['duihuan'] == 1){
                throw new ErrorException('此卡卷已经兑换');
            }
            if($integral == true){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->update(
                        'integral',
                        [
                            'duihuan'=>1,
                        ],
                        [
                            'id'=>$integral['id']
                        ])->execute();
                    if($data == true){

//                        $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
//                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null';
//                        $merchants = \Yii::$app->db1->createCommand($sql)->queryAll();
//                        $rest = $merchants[mt_rand(0, count($merchants) - 1)];
                        $model = yii::$app->db->createCommand()->insert(
                            'coupon',
                            [
                                'code'=>$rest['code'],
                                'userid'=>$openid->id,
                                'integral_id'=>$params['id'],
                                'end_time'=>$merchants[0]['end_time'],
                                'price'=>$merchants[0]['price'],
                                'state'=>1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($model == true){
                            $m = Yii::$app->db1->createCommand()->update(
                                'code',
                                [
                                    'openid' =>1,
                                ],
                                [
                                    'code' => $rest['code'],
                                ])->execute();
                            if($m == true){
                                $transaction->commit();
                            }
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
                return $merchants[0]['price'];
            }else{
                throw new ErrorException('此卡卷没有到5级无法兑换');
            }
        }else{
            $integral = (new \yii\db\Query())->select('a.id,a.userid,a.duihuan,a.card_id,b.id as bid,b.lerver')
                ->from('number as a,card as b')
                ->where('a.id=:id',['id'=>$params['nid']])
                ->andWhere('a.card_id=b.id')
                ->andWhere('b.lerver=5')
                ->andWhere('a.userid=:userid',['userid'=>$openid->id])
                ->one();
            $date = date('Y-m-d H:i:s');
            $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null and a.end_time>:end_time';
            $merchants = \Yii::$app->db1->createCommand($sql,[':end_time'=>$date])->queryAll();
            $rest = $merchants[mt_rand(0, count($merchants) -1)];
            if(empty($merchants)){
                throw new ErrorException('没有对应的优惠券请联系商家');
            }
            if($integral['duihuan'] == 1){
                throw new ErrorException('此卡卷已经兑换');
            }
            if($integral == true){
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $data = yii::$app->db->createCommand()->update(
                        'number',
                        [
                            'duihuan'=>1,
                        ],
                        [
                            'id'=>$integral['id']
                        ])->execute();
                    if($data == true){
//                        $sql = 'select a.id,a.price,a.end_time,b.openid,b.id as bid,b.url,b.activity_id,b.code
//                    from activity  a INNER JOIN code  b where a.id = 4 and a.id=b.activity_id and b.openid is null';
//                        $merchants = \Yii::$app->db1->createCommand($sql)->queryAll();
//                        $rest = $merchants[mt_rand(0, count($merchants) - 1)];
                        $model = yii::$app->db->createCommand()->insert(
                            'coupon',
                            [
                                'code'=>$rest['code'],
                                'userid'=>$openid->id,
                                'number_id'=>$params['nid'],
                                'end_time'=>$merchants[0]['end_time'],
                                'price'=>$merchants[0]['price'],
                                'state'=>1,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($model == true){
                            $m = Yii::$app->db1->createCommand()->update(
                                'code',
                                [
                                    'openid' =>1,
                                ],
                                [
                                    'code' => $rest['code'],
                                ])->execute();
                            if($m == true){
                                $transaction->commit();
                            }
                        }
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    throw $e;
                }
                return $merchants[0]['price'];
            }else{
                throw new ErrorException('此卡卷没有到5级无法兑换');
            }
        }
    }
    public function tail($params)
    {
        $openid = Wechat::openid($params);
        $data = (new \yii\db\Query())->select('code,userid,state,end_time,price')
            ->from('coupon')
            ->where('userid=:userid',['userid'=>$openid->id])
            ->andWhere('code=:code',['code'=>$params['code']])
            ->one();
        if($data == false){
            throw new ErrorException('无效的code');
        }
        if($data['state'] == 2){
            throw new ErrorException('此优惠券已经使用过,目前无法查看');
        }
        return ['url'=>'https://admin.youjingxi.com.cn/code/'.$data['code'].'.'.'jpg'];
//        header("Content-Type: text/plain");
//        set_time_limit(0);
//        $infoString = "Hello World" . "\n";
//        while( isset($infoString) )
//        {
//            echo $infoString;
//            flush();
//            ob_flush();
//            sleep(5);
//        }
    }
}
