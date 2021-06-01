<?php

namespace api\modules\v1\models;

use backend\modules\v1\models\Card;
use backend\modules\v1\models\Oss;
use common\base\ErrorException;
use phpDocumentor\Reflection\Types\False_;
use Yii;
use yii\caching\DbDependency;
use yii\data\Pagination;

/**
 * This is the model class for table "image".
 *
 * @property int $id 自增
 * @property string $name 形象名称
 * @property int $cover 图片id
 * @property string $describe 形象描述
 * @property int $state 1展示 2不展示
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cover', 'describe', 'state'], 'required'],
            [['cover', 'state'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['describe'], 'string', 'max' => 200],
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
            'cover' => 'Cover',
            'describe' => 'Describe',
            'state' => 'State',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    public static function manager($key,$type = 1,$level = null){
//        $cache = Cache::get('cache_manager_list');
        if(!$key) return [ '-1' ];
        switch ($type){
            case 1:
                if(isset($cache[$key])) {
                    return $cache[$key];
                }else{
                    return self::setManager($key,$level);
                }
                break;
            case 2:
//                self::clearManagerCache($key,$level);
                return true;
                break;
            default:
                break;
        }
        return true;
    }
    public static function setManager($key,$level){
        if($level == null) $level = Db::name('merchant_manager')->where(['manager_id'=>['eq',$key]])->value('manager_level');
        $cache[$key] = Cache::get('cache_manager_list');
        if ($level == 1) {
            $idlist = Db::name('merchant_manager')->where(['manager_pid'=>['eq',$key]])->column('manager_id');
            $this_value = [$key];
            if($idlist) $this_value = array_merge($this_value,$idlist);
            $cache[$key] = $this_value;
        }else if ($level == 2){
            $cache[$key] = [$key];
        }
//        Cache::set('cache_manager_list',$cache);
        return $cache[$key];
    }
    /**
     * 列表
     * */
    public function show($params)
    {
        $pageSize = !empty($params['size'])?$params['size']:10;
        $state = !empty($params['state'])?$params['state']:null;
        $model = (new \yii\db\Query())->select('a.id,a.name,a.cover,a.state,a.describe,a.create_at,b.id as bid,b.url')
                ->from('image as a,oss as b')
                ->where('a.cover=b.id')
                ->andFilterWhere(['=', 'a.state', $state]);
            $pages = new Pagination(['totalCount' => $model->count(), 'pageSize' => $pageSize]);
            $data = $model->offset($pages->offset)->limit($pages->limit)->all();
            return ['item' => $data, 'pages' => $pages];
    }
    /**
     * 详情
     * */
    public function details($params)
    {
        $model = (new \yii\db\Query())->select('a.id,a.name,a.cover,a.describe,a.create_at,b.id as bid,b.url')
            ->from('image as a,oss as b,')
            ->where('a.id=:id',['id'=>$params['id']])
            ->andWhere('a.cover=b.id')
            ->one();

        $array = explode(',',$model['describe'],4);
//        return $array;die;
        foreach ($array as $key=>$value){
            $data[] = (new \yii\db\Query())->select('id,url')
                ->from('oss')
                ->where('id=:id',['id'=>$value])
                ->all();
        }
        $a =(new \yii\db\Query())->select('a.id,a.image_id,a.type,a.image,
        b.id as bid,b.url')
            ->from('styleimage as a,oss as b')
            ->where('a.image_id=:image_id',['image_id'=>$params['id']])
            ->andWhere('a.image=b.id')
            ->all();
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $data);
        //
        return ['items'=>$model,'image'=>$result,'card'=>$a];

    }
    /**
     * 签到天数  积分数量
     * */
    public function payment($params)
    {
        $openid = Wechat::openid($params);
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
            ->andWhere(['!=','integral' ,' null'])
            ->all();
        if(empty($integral)){
            //签到的天数
            $sign = (new \yii\db\Query())->select('a.integral,a.user_id,a.state,a.create_at,a.card_id,b.id,b.cover_id,
            c.id as cid,c.url')
                ->from('integral as a')
                ->leftJoin('card  b','b.id=a.card_id')
                ->leftJoin('oss  c','c.id=b.cover_id')
                ->where('a.state=1')
                ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                ->all();
            $intral = (new \yii\db\Query())->select('
        b.id,b.user_id,b.card_id,b.state,b.duihuan,b.integral,
        a.id as aid,a.styimage_id,a.cover_id,max(a.lerver) as lerver,
        c.id as cid,c.image_id,c.type,d.id as did,d.name,f.id as fid,f.url')
                ->from('card as a')
                ->innerJoin('integral b','b.card_id=a.id')
                ->innerJoin('styleimage c','c.id=a.styimage_id')
                ->innerJoin('image d','d.id=c.image_id')
                ->innerJoin('oss f','f.id=a.cover_id')
                ->where('b.user_id=:user_id',['user_id'=>$openid->id])
                ->groupBy(['a.styimage_id'])
                ->all();
            $num = (new \yii\db\Query())->select('a.id as nid,a.userid,a.style_id,a.card_id,
            b.id as bid,b.styimage_id,b.cover_id,max(b.lerver) as lerver,
            c.id as cid,c.image_id,c.type,d.id as did,d.name,e.id as eid,e.url')
                ->from('number as a,card as b,styleimage as c,image as d,oss as e')
                ->where('a.card_id=b.id')
                ->andWhere('b.styimage_id=c.id')
                ->andWhere('c.image_id=d.id')
                ->andWhere('b.cover_id=e.id')
                ->andWhere('a.userid=:userid',['userid'=>$openid->id])
                ->groupBy(['b.styimage_id'])
                ->all();
            //有多少积分            签到的时间           拥有的卡片            NFC识别的
            return ['subtotal'=>0,'sign'=>$sign,'integral'=>$intral,'NFC'=>$num];
        }
        if(empty($inte)){

            foreach ($integral as $value){
                $data[] = $value['integral'];
            }
            //积分有多少
            $money = array_sum($data);
            //签到的天数
            $sign = (new \yii\db\Query())->select('a.integral,a.user_id,a.state,a.create_at,a.card_id,b.id,b.cover_id,
            c.id as cid,c.url')
                ->from('integral as a')
                ->leftJoin('card  b','b.id=a.card_id')
                ->leftJoin('oss  c','c.id=b.cover_id')
                ->where('a.state=1')
                ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
                ->all();
            $intral = (new \yii\db\Query())->select('
        b.id,b.user_id,b.card_id,b.state,b.duihuan,b.integral,
        a.id as aid,a.styimage_id,a.cover_id,max(a.lerver) as lerver,
        c.id as cid,c.image_id,c.type,d.id as did,d.name,f.id as fid,f.url')
                ->from('card as a')
                ->innerJoin('integral b','b.card_id=a.id')
                ->innerJoin('styleimage c','c.id=a.styimage_id')
                ->innerJoin('image d','d.id=c.image_id')
                ->innerJoin('oss f','f.id=a.cover_id')
                ->where('b.user_id=:user_id',['user_id'=>$openid->id])
                ->groupBy(['a.styimage_id'])
                ->all();
            $num = (new \yii\db\Query())->select('a.id as nid,a.userid,a.style_id,a.card_id,
            b.id as bid,b.styimage_id,b.cover_id,max(b.lerver) as lerver,
            c.id as cid,c.image_id,c.type,d.id as did,d.name,e.id as eid,e.url')
                ->from('number as a,card as b,styleimage as c,image as d,oss as e')
                ->where('a.card_id=b.id')
                ->andWhere('b.styimage_id=c.id')
                ->andWhere('c.image_id=d.id')
                ->andWhere('b.cover_id=e.id')
                ->andWhere('a.userid=:userid',['userid'=>$openid->id])
                ->groupBy(['b.styimage_id'])
                ->all();
            //有多少积分            签到的时间           拥有的卡片            NFC识别的
            return ['subtotal'=>$money,'sign'=>$sign,'integral'=>$intral,'NFC'=>$num];
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
        //签到的天数
        $sign = (new \yii\db\Query())->select('a.integral,a.user_id,a.state,a.create_at,a.card_id,b.id,b.cover_id,
            c.id as cid,c.url')
            ->from('integral as a')
            ->leftJoin('card  b','b.id=a.card_id')
            ->leftJoin('oss  c','c.id=b.cover_id')
            ->where('a.state=1')
            ->andWhere('a.user_id=:user_id',['user_id'=>$openid->id])
            ->all();

        $intral = (new \yii\db\Query())->select('
        a.id as aid,a.styimage_id,a.cover_id,max(a.lerver) as lerver,
        b.id as bid,b.user_id,b.card_id,b.state,b.duihuan,b.integral,
        c.id as cid,c.image_id,c.type,d.id as did,d.name,f.id as fid,f.url')
            ->from('card as a')
            ->innerJoin('integral b','b.card_id=a.id')
            ->innerJoin('styleimage c','c.id=a.styimage_id')
            ->innerJoin('image d','d.id=c.image_id')
            ->innerJoin('oss f','f.id=a.cover_id')
            ->where('b.user_id=:user_id',['user_id'=>$openid->id])
            ->groupBy('a.styimage_id')
            ->all();
        foreach ($intral as $key=>$value){
//            return $value;
            $in[] = (new \yii\db\Query())->select('a.id,a.styimage_id,a.cover_id,a.lerver,
            b.id as bid,b.url
            ')
                ->from('card as a,oss as b')
                ->where('a.lerver=:lerver',['lerver'=>$value['lerver']])
                ->andWhere('a.styimage_id=:styimage_id',['styimage_id'=>$value['styimage_id']])
                ->andWhere('a.cover_id=b.id')
                ->all();
        }
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $in);
        $num = (new \yii\db\Query())->select('a.id as nid,a.userid,a.style_id,a.card_id,
        b.id as bid,b.styimage_id,b.cover_id,max(b.lerver) as lerver,
        c.id as cid,c.image_id,c.type,d.id as did,d.name,f.id as fid,f.url')
            ->from('number as a,card as b,styleimage as c,image as d,oss as f')
            ->where('a.card_id=b.id')
            ->andWhere('b.styimage_id=c.id')
            ->andWhere('c.image_id=d.id')
            ->andWhere('b.cover_id=f.id')
            ->andWhere('a.userid=:userid',['userid'=>$openid->id])
            ->groupBy(['b.styimage_id'])
            ->all();
                //有多少积分            签到的时间      拥有的卡片            NFC识别的
        return ['subtotal'=>$subtotal,'sign'=>$sign,'integral'=>$result,'NFC'=>$num];
    }
    /**
     * 形象对应的卡册  card里的id
     * */
    public function card($params)
    {
        $pagesize = !empty($params['size'])?$params['size']:10;
        $data = (new \yii\db\Query())->select('a.id as card_id,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url')
            ->from('card as a,oss as b')
            ->where('a.styimage_id=:styimage_id',['styimage_id'=>$params['id']])
            ->andWhere('a.cover_id=b.id');
        $pages = new Pagination(['totalCount'=>$data->count(),'pageSize'=>$pagesize]);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        return ['items'=>$model,'pages'=>$pages];
    }
    /**
     * 拥有的卡册
     * */
    public function number($params)
    {
        $openid = Wechat::openid($params);
        $data = (new \yii\db\Query())->select('a.id,c.id as cid,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url
        ,c.card_id,c.user_id,')
                ->from('card as a,oss as b,integral as c')
                ->where('c.user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('a.styimage_id=:styimage_id',['styimage_id'=>$params['id']])
                ->andWhere('a.cover_id=b.id')
                ->andWhere('c.card_id=a.id')
                ->orderBy(['a.lerver' => SORT_DESC])
                ->all();
            $model = (new \yii\db\Query())->select('c.id as nid,a.id,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url
        ,c.card_id,c.userid,')
                ->from('card as a,oss as b,number as c')
            ->where('a.styimage_id=:styimage_id',['styimage_id'=>$params['id']])
                ->andWhere('a.cover_id=b.id')
                ->andWhere('c.userid=:userid',['userid'=>$openid->id])
                ->andWhere('c.card_id=a.id')
                ->orderBy(['a.lerver' => SORT_DESC])
                ->all();
        $dat = (new \yii\db\Query())->select('a.id,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url
        ')
            ->from('card as a,oss as b,')
            ->where('a.styimage_id=:styimage_id',['styimage_id'=>$params['id']])
            ->andWhere('a.cover_id=b.id')
            ->orderBy(['a.lerver' => SORT_DESC])
            ->all();
            return ['items'=>$data,'model'=>$dat,'data'=>$model];

    }
//    public function numb($params)
//    {
//        $data = (new \yii\db\Query())->select('a.id,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url
//        ')
//            ->from('card as a,oss as b,')
//            ->where('a.styimage_id=:styimage_id',['styimage_id'=>$params['id']])
//            ->andWhere('a.cover_id=b.id')
//            ->orderBy(['a.lerver' => SORT_DESC])
//            ->all();
//        return $data;
//    }
    /**
     * 积分兑换
     * */
    public function change($params)
    {
        $openid = Wechat::openid($params);
        $redis = yii::$app->redis;
        $id = $redis->get('DH::'.$openid->id);
        if($id == 1){
            throw new ErrorException('3秒之内只能兑换一次');
        }
        $lerver = $params['lerver'] == '0' ? '1' :($params['lerver']>=1 ? $params['lerver']+1 : $params['lerver']+1);
        if($lerver > 5){
            throw new ErrorException('已经是最高等级无法再升级');
        }
        $data = (new \yii\db\Query())->select('b.id,b.styimage_id,b.lerver,b.cover_id,
            c.id as cid,c.url')
            ->from('card as b')
            ->innerJoin('oss c','c.id=b.cover_id')
            ->where('b.styimage_id=:styimage_id', ['styimage_id' => $params['id']])
            ->andWhere('b.lerver=:lerver',['lerver'=>$lerver])
            ->one();
        $you = (new \yii\db\Query())->select('user_id,card_id')
            ->from('integral')
            ->where('user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('card_id=:card_id',['card_id'=>$data['id']])
            ->one();
        if($you){
            throw new ErrorException('已经拥有此卡册');
        }
        if(empty($data)){
            throw new ErrorException('无效的id');
        }
            $integral = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state!=2')
                ->andWhere('user_id=:user_id', ['user_id' => $openid->id])
                ->andWhere(['!=', 'integral', ' null'])
                ->all();
            $inte = (new \yii\db\Query())->select('integral,user_id,state')
                ->from('integral')
                ->where('state=2')
                ->andWhere('user_id=:user_id', ['user_id' => $openid->id])
                ->all();
            //没有消费
            if (empty($integral)) {
                throw new ErrorException('目前还没有积分无法兑换');
            }
            if (empty($inte)) {
                foreach ($integral as $value) {
                    $d[] = $value['integral'];
                }
                //积分有多少
                $money = array_sum($d);
                $shengyu = $money - ($data['lerver']*100);
                if ($shengyu >= 0) {
                    $transaction = yii::$app->db->beginTransaction();
                    try {
                        $model = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'user_id' => $openid->id,
                                'card_id' => $data['id'],
                                'state' => 6,
                                'upgrade' => 1,
                                'create_at' => date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if ($model == true) {
                            $rest = yii::$app->db->createCommand()->insert(
                                'integral',
                                [
                                    'integral'=> $data['lerver']*100,
                                    'user_id'=> $openid->id,
                                    'state'=> 2,
                                    'create_at'=>date('Y-m-d H:i:s'),
                                ]
                            )->execute();
                            if($rest == true){
                                $transaction->commit();
                                $redis->setex('DH::'.$openid->id,3,0);
                                $redis->incr('DH::'.$openid->id);
                                return [
                                    'state'=>1,
                                    'integral'=>0,
                                    'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$data['url'],
                                ];
                            }
                        }
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }

                } else {
                    throw new ErrorException('目前积分不足');
                }
            }
            if (empty($integral) || empty($inte)) {
                throw new ErrorException('目前还没有积分无法兑换');
            }
            foreach ($integral as $value) {
                $f[] = $value['integral'];
            }
            foreach ($inte as $value) {
                $dat[] = $value['integral'];
            }
            //积分有多少
            $money = array_sum($f);
            //兑换有多少
            $change = array_sum($dat);
            $subtotal = $money - $change;
            $sheng = $subtotal - ($data['lerver']*100);
            if ($sheng >= 0) {
                $transaction = yii::$app->db->beginTransaction();
                try {
                    $model = yii::$app->db->createCommand()->insert(
                        'integral',
                        [
                            'user_id' => $openid->id,
                            'card_id' => $data['id'],
                            'state' => 6,
                            'upgrade' => 1,
                            'create_at' => date('Y-m-d H:i:s'),
                        ]
                    )->execute();
                    if ($model == true) {
                        $rest = yii::$app->db->createCommand()->insert(
                            'integral',
                            [
                                'integral'=> $data['lerver']*100,
                                'user_id'=> $openid->id,
                                'state'=> 2,
                                'create_at'=>date('Y-m-d H:i:s'),
                            ]
                        )->execute();
                        if($rest == true){
                            $transaction->commit();
                            $redis->setex('DH::'.$openid->id,3,0);
                            $redis->incr('DH::'.$openid->id);
                            return [
                                'state'=>1,
                                'integral'=>0,
                                'picurl'=>'http://gbackend.youjingxi.net.cn/uploads/'.$data['url'],
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }else {
                throw new ErrorException('目前积分不足');
            }
    }
    /**
     * 更多里的详情页面
     * */
    public function cardbook($params)
    {
        $openid = Wechat::openid($params);
        $a =(new \yii\db\Query())->select('a.id,a.image_id,a.type,a.image,
        b.id as bid,b.url')
            ->from('styleimage as a,oss as b')
            ->where('a.image_id=:image_id',['image_id'=>$params['id']])
            ->andWhere('a.image=b.id')
            ->all();
        if($a == false){
            throw new ErrorException('没有对应的星级卡');
        }
        foreach ($a as $key=>$value){
            $data[] = (new \yii\db\Query())->select('a.id,c.id as cid,a.styimage_id,a.cover_id,a.lerver,b.id as bid,b.url
        ,c.card_id,c.user_id,')
                ->from('card as a,oss as b,integral as c')
                ->where('c.user_id=:user_id',['user_id'=>$openid->id])
                ->andWhere('a.styimage_id=:styimage_id',['styimage_id'=>$value['id']])
                ->andWhere('a.cover_id=b.id')
                ->andWhere('c.card_id=a.id')
                ->orderBy(['a.lerver' => SORT_DESC])
                ->count();
        }
        foreach ($data as $key=>$v){
            $s[] = round($v/5*100);
        }
        return ['items'=>$a,'count'=>$data,'percent'=>$s];
    }
    /**
     * 轮播
     * */
    public function rotation($params)
    {
        $data = (new \yii\db\Query())->select('id,image')
            ->from('rotation')
            ->all();
        for ($i=0; $i < count($data) ; $i++){
            $arr[] = explode(',',$data[$i]['image']);
        }
        $array = array_merge($arr);
        foreach ($array as $key=>$value){
            $model[] = Oss::findAll(['id'=>$value]);
        }
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $model);
        return $result;
    }

}
