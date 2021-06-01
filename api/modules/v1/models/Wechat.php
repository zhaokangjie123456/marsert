<?php

namespace api\modules\v1\models;

use common\base\ErrorException;
use Yii;

/**
 * This is the model class for table "wechat".
 *
 * @property int $id 自增
 * @property string $openid 微信openid
 * @property string $nikname
 * @property string $mobile
 * @property string $unionid 微信返回
 * @property int $sex 性别
 * @property string $headimgurl 用户头像
 * @property int $superior_id 用户属于哪个渠道商
 * @property string $session_key
 * @property string $token 自定义生成的token
 * @property string $access_token
 * @property string $refresh_token
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Wechat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wechat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sex', 'superior_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['openid', 'nikname'], 'string', 'max' => 200],
            [['mobile'], 'string', 'max' => 80],
            [['unionid', 'headimgurl', 'session_key', 'token', 'access_token', 'refresh_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'nikname' => 'Nikname',
            'mobile' => 'Mobile',
            'unionid' => 'Unionid',
            'sex' => 'Sex',
            'headimgurl' => 'Headimgurl',
            'superior_id' => 'Superior ID',
            'session_key' => 'Session Key',
            'token' => 'Token',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
    /**
     * 判断有没有传openid
     * */
    public static function openid($openid)
    {
        $openi = Wechat::findOne(['openid'=>$openid]);
        if($openi == false){
            throw new ErrorException('无效的openid');
        }
        return $openi;
    }
}
