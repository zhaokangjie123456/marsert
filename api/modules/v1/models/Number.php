<?php

namespace api\modules\v1\models;

use api\sdk\Token;
use common\base\ErrorException;
use Yii;
use yii\redis;

/**
 * This is the model class for table "number".
 *
 * @property int $id 自增
 * @property int $style_id 形象类型id
 * @property int $userid 用户id
 * @property string $num_num 具体的编号
 * @property int $card_id 具体的星级卡
 * @property string $create_at 创建时间
 * @property string $update_at 修改时间
 */
class Number extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'num_num'], 'required'],
            [['style_id', 'userid', 'card_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['num_num'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_id' => 'Style ID',
            'userid' => 'Userid',
            'num_num' => 'Num Num',
            'card_id' => 'Card ID',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    /**
     * 计算中奖
     * */
    public static function get_rand($probability)
    {
        // 概率数组的总概率精度
        $max = array_sum($probability);
        foreach ($probability as $key => $val) {
            $rand_number = mt_rand(1, $max);//从1到max中随机一个值
            if ($rand_number <= $val) {//如果这个值小于等于当前中奖项的概率，我们就认为已经中奖
                return $key;
            } else {
                $max -= $val;//否则max减去当前中奖项的概率，然后继续参与运算
            }
        }
        /**
         *
         */
    }
    public function prize($params)
    {
        // 概率比例
        /* 接下来我们通过PHP配置奖项。 */
        $data = (new \yii\db\Query())->select('a.id,a.styimage_id,a.rate,a.lerver,b.id as bid,b.image_id,b.type,
        c.id as cid,c.name')
            ->from('card as a,styleimage as b,image as c')
            ->where('a.styimage_id=b.id')
            ->andWhere('b.image_id=c.id')
            ->all();

        foreach ($data as $key => $val) {
            $probability[$key] = $val["rate"];
        }
        $n = self::get_rand($probability);
        $res['yes'] = $data[$n]["type"].$data[$n]['name'].','.'等级：'.$data[$n]['lerver'];//$res['yes'] =$data[$n][0];

        unset($data[$n]); // 将中奖项从数组中剔除，剩下未中奖项
        shuffle($data); // 将其它奖项顺序打乱
        $func = create_function('$x', 'return $x["name"];');
        $res['no'] = array_map($func, $data);  // 除了中奖外的其他数据
        return $res['yes'];
    }
    /**
     *
     * */
    public function show($params)
    {
        $model = (new \yii\db\Query())->select('voucher_no')
            ->from('voucher_detail')
            ->where(['>','voucher_no','005400000490'])
            ->all();
                $count = count($model);
        var_dump($count);
        $redis = yii::$app->redis;
        foreach ($model as $key=>$value){
             $redis->sadd('pys',$value['voucher_no']);
        }
            $a = $redis->SRANDMEMBER('py',400);


        foreach ($a as $key=>$va){
                    $data[] = yii::$app->db->createCommand()->delete(
                        'voucher_detail',
                        [
                           'voucher_no' =>$va
                        ]
                    )->execute();
        }
        $data = $redis->srem('pys',$a);
        if($data == true){
            return '成功';
        }else{
            return '失败';
        }
    }
}
