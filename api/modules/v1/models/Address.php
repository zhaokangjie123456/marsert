<?php

namespace api\modules\v1\models;

use common\base\ErrorException;
use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "address".
 *
 * @property int $id 自增
 * @property int $user_id 用户id
 * @property string $province 省份
 * @property string $city 市
 * @property string $district 区
 * @property string $address 具体地址
 * @property string $mobile 收货手机号
 * @property string $name 收货人姓名
 * @property int $is_default 1 默认 2 非默认
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'province', 'city', 'district', 'address', 'mobile', 'name', 'is_default'], 'required'],
            [['user_id', 'is_default'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['province', 'city'], 'string', 'max' => 80],
            [['district', 'name'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'mobile' => 'Mobile',
            'name' => 'Name',
            'is_default' => 'Is Default',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    /**
     * 添加收货地址
     * */
    public function addre($params)
    {
        $openid = Wechat::openid($params['openid']);
        //查询goods表中的限制字段判断如果小于就可以添加
        //    $max = (new \yii\db\Query())->select('a.id,a.user_id,a.card_id,a.duihuan,b.styimage_id,max(b.lerver)')
//            ->from('integral as a')
//            ->innerJoin('card b','b.id=a.card_id')
//            ->where('a.user_id=:user_id',['user_id'=>$openid->id])
//            ->andWhere('b.lerver=5')
//            ->groupBy('b.styimage_id')
//            ->count();
//        $config = (new \yii\db\Query())->select('address')
//            ->from('config')
//            ->one();
//        if($max < $config){
//            throw new ErrorException('目前没有资格添加收货地址');
//        }
        $redis = yii::$app->redis;
        $id = $redis->get('ADDRE:'.$openid->id);
//        if($id == 1){
//            throw new ErrorException('已添加过地址信息');
//        }
        if(empty($params['province'])){
            throw new ErrorException('省名称不能为空');
        }
        if(empty($params['city'])){
            throw new ErrorException('市名称不能为空');
        }
        if(empty($params['district'])){
            throw new ErrorException('区名称不能为空');
        }
        if(empty($params['address'])){
            throw new ErrorException('具体地址不能为空');
        }
        if(!preg_match('/^[1][1-9][0-9]{9}$/', $params['mobile'])){
            throw new ErrorException('手机号格式不正确');
        }
        if(empty($params['name'])){
            throw new ErrorException('收货人姓名名称不能为空');
        }
        $model = yii::$app->db->createCommand()->insert(
            'address',
            [
                'province'=>   $params['province'],
                'city'=>       $params['city'],
                'district'=>   $params['district'],
                'address'=>    $params['address'],
                'name'=>       $params['name'],
                'mobile'=>     $params['mobile'],
                'user_id'=>    $openid->id,
                'is_default'=>   2,
                'create_at'=>   date('Y-m-d H:i:s'),
            ]
        )->execute();
        if($model == true){
           // $redis->set('ADDRE:'.$openid->id);
            $redis->incr('ADDRE:'.$openid->id);
            return '地址信息添加成功';
        }else{
            throw new ErrorException('地址添加失败');
        }
    }
    /**
     * 修改
     * */
    public function modify($params)
    {
        $openid = Wechat::openid($params['openid']);
        if(empty($params['province'])){
            throw new ErrorException('省名称不能为空');
        }
        if(empty($params['city'])){
            throw new ErrorException('市名称不能为空');
        }
        if(empty($params['district'])){
            throw new ErrorException('区名称不能为空');
        }
        if(empty($params['address'])){
            throw new ErrorException('具体地址不能为空');
        }
        if(!preg_match('/^[1][1-9][0-9]{9}$/', $params['mobile'])){
            throw new ErrorException('手机号格式不正确');
        }
        if(empty($params['name'])){
            throw new ErrorException('收货人姓名名称不能为空');
        }
        if(empty($params['is_default'])){
            throw new ErrorException('地址是否是默认 不能为空');
        }
        $data = Address::findOne(['id'=>$params['id'],'user_id'=>$openid->id]);
        if($data == false){
            throw new ErrorException('无效的收货地址id');
        }
        $model = yii::$app->db->createCommand()->update(
            'address',
            [
                'province'=>   $params['province'],
                'city'=>       $params['city'],
                'district'=>   $params['district'],
                'address'=>    $params['address'],
                'name'=>       $params['name'],
                'mobile'=>     $params['mobile'],
                'user_id'=>    $openid->id,
                'is_default'=> $params['is_default'],
                'create_at'=>   date('Y-m-d H:i:s'),
            ],
            [
                'id'=>$data->id
            ]
        )->execute();
        if($model == true){
            return '地址信息修改成功';
        }else{
            throw new ErrorException('地址修改失败');
        }
    }
    /**
     * 删除
     * */
    public function strick($params)
    {
        $openid = Wechat::openid($params['openid']);
        $data = Address::findOne(['id'=>$params['id'],'user_id'=>$openid->id]);
        if($data == false){
            throw new ErrorException('无效的收货地址id');
        }
        $order = GameOrder::findAll(['address_id'=>$data->id]);
        if($order == true){
            throw new ErrorException('此收货地址已使用无法删除');
        }
        $model = yii::$app->db->createCommand()->delete(
            'address',
        [
            'id'=>$data->id
        ]
        )->execute();
        if($model == true){
            return '收货信息删除成功';
        }else{
            throw new ErrorException('收货信息删除失败');
        }
    }
    /**
     * 列表
     * */
    public function show($params)
    {

//        $sql="select * from user";
//        $res=mysql_query($sql);
//        $row=mysql_fetch_row($res);
        $row = (new \yii\db\Query())->select('*')
            ->from('wechat');

        while($row) {
            echo 'A row';
        }
//        $transaction = yii::$app->db->beginTransaction();
//        try {
//            $id = (new \yii\db\Query())->select('id,name,user_id,province,city,district,address,mobile,is_default,create_at')
//                ->from('address')
//                ->where(['>','create_at','2020-10-10'])
//                ->one();
//            return $id;
//            yii::$app->db->createCommand()->update(
//                'address',
//                [
//                    'name'=>$id->name,
//                    'user_id'=>'1',
//                    'province'=>'1',
//                    'city'=>'1',
//                    'district'=>'1',
//                    'address'=>'1',
//                    'mobile'=>'1',
//                    'is_default'=>'2',
//                ],
//                [
//                    'id'=>$id->id
//                ]
//            )->execute();
//            $transaction->commit();
//            }catch (\Throwable $e){
//                $transaction->rollBack();
//                throw $e;
//            }

//        $pagesize = !empty($params['size'])?$params['size']:10;
//        $openid = Wechat::openid($params['openid']);
//        $model = (new \yii\db\Query())->select('id,user_id,province,city,district,address,mobile,name,is_default,create_at')
//            ->from('address')
//            ->where('user_id=:user_id',['user_id'=>$openid->id]);
//        $pages = new Pagination(['totalCount'=>$model->count(),'pageSize'=>$pagesize]);
//        $data = $model->offset($pages->offset)->limit($pages->limit)->all();
//        return ['items'=>$data,'pages'=>$pages];
    }
    /**
     * 详情
     * */
    public function details($params)
    {
        $openid = Wechat::openid($params['openid']);
        $model = (new \yii\db\Query())->select('id,user_id,province,city,district,address,mobile,name,is_default,create_at')
            ->from('address')
            ->where('user_id=:user_id',['user_id'=>$openid->id])
            ->andWhere('id=:id',['id'=>$params['id']])
            ->one();
        if($model == false){
            throw new ErrorException('无效的收货id');
        }
        return $model;
    }
    /**
     * 二维码列表
     * */
    public function code($params)
    {
        $openid = Wechat::openid($params['openid']);
        $model = (new \yii\db\Query())->select('a.code,b.id,b.url')
            ->from('code as a,oss as b')
            ->where('a.code=b.id')
            ->one();
        return $model;
    }
}
