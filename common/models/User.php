<?php

namespace common\models;

use common\base\BaseModel;
use common\behavior\PointBehavior;
use common\behavior\TimeBehavior;
use common\lib\RegexValidator;
use Yii;
use yii\base\Exception;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $uid
 * @property string $nick
 * @property string $name
 * @property string $avatar
 * @property integer $mobile
 * @property string $email
 * @property integer $points
 * @property integer $user_type
 * @property integer $user_type_imgs
 * @property integer $name_card
 * @property string $wechat_openid
 * @property integer $user_status
 * @property integer $update_at
 * @property integer $create_at
 */
class User extends BaseModel implements IdentityInterface
{

    /**
     * 用户类型
     */
    const TYPE_COMMON = 1;//普通用户
    const TYPE_SELLER = 2;//销售/门店导购
    const TYPE_DESIGNER = 3;//设计师
    const TYPE_TAILER= 4;//零售经销商
    const TYPE_PROJECT = 5;//项目经销商
    const TYPE_ANZH = 6;//安装商/安装人员
    const TYPE_EMPLOYEE = 7;//尚飞员工

    const NO_DELETE = 1;//启用
    const IS_DELETE = 2;//禁用

    /**
     * 事件
     */
    const EVENT_POINTS = 'event_points';//赠送积分事件

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['points', 'update_at', 'create_at', 'login_at'], 'integer'],
            //手机号码
            [['mobile'], RegexValidator::className(), 'method' => 'mobile', 'message' => '手机格式不正确'],
            [['mobile'], 'unique', 'message' => '手机号必须唯一'],
            //积分
            [['points'], RegexValidator::className(), 'method' => 'unsigned', 'message' => '积分必须为非负整数'],
            //authKey
            [['authKey'], 'string'],
            [['authKey'], 'required', 'message' => 'authKey号不能为空'],
            [['authKey'], 'unique', 'message' => 'authKey号必须唯一'],
            [['create_at', 'update_at'], 'default', 'value' => time()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => '用户ID',
            'authKey' => 'authKey',
            'nick' => '用户微信昵称',
            'name' => '用户真实姓名',
            'avatar' => '用户微信头像',
            'mobile' => '用户手机号码',
            'email' => '用户邮箱',
            'name_card' => '名片',
            'points' => '积分',
            'user_type' => '用户类型（1-普通用户；2-销售/门店导购；3-设计师；4-零售经销商；5-项目经销商；6-安装商/安装人员；7尚飞员工）',
            'user_type_imgs' => '用户类型图片',
            'wechat_openid' => '微信Open Id',
            'user_status' => '状态（1-启用；2-禁用）',
            'update_at' => '更新时间',
            'create_at' => '创建时间',
            'login_at' => '最近登录时间',
        ];
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * 公共模型的行为，比如对某些字段自动更新时间戳操作
     * @return array
     */
    public function behaviors()
    {
        return [
            'timeStamp' => [
                'class' => TimeBehavior::className(),
                'create' => ['create_at', 'login_at'],
                'update' => 'update_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($uid)
    {
        return static::findOne($uid);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['authKey' => $token, 'user_status' => self::NO_DELETE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * 获取手机号
     **/
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            if($insert){ //插入操作
            }
            return true;
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public function login($param)
    {
        if (empty($param['mobile'])) {
            return ['code' => '-40301', 'msg' => '手机号码不能为空'];
        }
        $mobile = $param['mobile'];
        $user = static::findOne(['mobile' => $mobile]);

        $reg = false;
        if (empty($user)) {
            $user = new static();
            $user->mobile = $mobile;
            $user->authKey = $this->genAuthKey();
            if (!$user->validate()) {
                return ['code' => '-40302', 'msg' => reset($user->getFirstErrors())];
            }
            $ret = $user->save();
            if ($ret['code'] < 0) {
                return ['code' => '-40303', 'msg' => $ret['code']];
            }
            $reg = true;
        }
        Yii::$app->user->login($user, 24*24*60);
        $user->touch('login_at');//更新登录时间
        return ['code' => '20000', 'msg' => $reg ? '注册成功' : '登录成功'];

    }

    /**
     * 生成唯一用户authKey
     * @return string
     */
    protected function genAuthKey() {
        $authKey = Yii::$app->security->generateRandomString();
        $exist = static::find()->where(['authKey' => $authKey])->exists();
        if($exist){
            $this->genAuthKey();
        }
        return $authKey;
    }

    /**
     * 关联购物车
     **/
    public function getCart() {
        return $this->hasOne(Cart::className(), ['uid' => 'uid']);
    }

    /**
     * 获取信息
     * @param $where array
     * @return array|boolean
     **/
    public function _get_info($where = []) {
        if (empty($where)) {
            return false;
        }

        $obj = self::findOne($where);
        if (!empty($obj)) {
            return $obj->toArray();
        }
        return false;
    }

    /**
     * 获取信息-关联
     * @param $where array
     * @return array|boolean
     **/
    public function _get_info_all($where = []) {
        if (empty($where)) {
            return false;
        }

        $obj = self::find();
        if (!empty($obj)) {
            return $obj->where($where)->joinWith('cart')->asArray(true)->one();
        }
        return false;
    }

    /**
     * 获取列表
     * @param $where array
     * @param $order string
     * @return array|boolean
     */
    public function _get_list($where = [], $order = 'created_at desc', $page = 1, $limit = 20) {
        $_obj = self::find();
        if (isset($where['sql']) || isset($where['params'])) {
            $_obj->where($where['sql'], $where['params']);
        } else if (is_array($where)) {
            $_obj->where($where);
        }

        $_obj->orderBy($order);

        if (!empty($limit)) {
            $offset = max(($page - 1), 0) * $limit;
            $_obj->offset($offset)->limit($limit);
        }
        return $_obj->asArray(true)->all();
    }

    /**
     * 获取总条数
     * @param $where array
     * @return int
     */
    public function _get_count($where = []) {
        $_obj = self::find();
        if (isset($where['sql']) || isset($where['params'])) {
            $_obj->where($where['sql'], $where['params']);
        } else {
            $_obj->where($where);
        }
        return intval($_obj->count());
    }

    /**
     * 添加记录-返回新插入的自增id
     **/
    public static function _add($data) {
        if (!empty($data) && !empty($data['username'])) {
            try {
                $_mdl = new self;

                foreach ($data as $k => $v) {
                    $_mdl->$k = $v;
                }
                if(!$_mdl->validate()) {//校验数据
                    return false;
                }
                $ret = $_mdl->insert();
                if ($ret !== false) {
                    return self::getDb()->getLastInsertID();
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * 保存记录
     * @param $data array
     * @return array|boolean
     */
    public function _save($data) {
        if (!empty($data)) {
            $_mdl = new self();

            try {
                foreach ($data as $k => $v) {
                    $_mdl->$k = $v;
                }

                if (!empty($data['uid'])) {//修改
                    $id = $data['uid'];
                    $ret = $_mdl->updateAll($data, ['uid' => $id]);
                } else {//增加
                    $ret = $_mdl->insert();
                }

                if ($ret !== false) {
                    return true;
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * 删除记录
     * @param $where array
     * @return array|boolean
     */
    public static function _delete($where) {
        if (empty($where)) {
            return false;
        }
        try {
            return (new self)->deleteAll($where);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取用户购物车
     * @return array|boolean
     */
    public static function _get_cart($uid){
        if(empty($uid)){
            return false;
        }
        $cart = Cart::findOne(['uid' => $uid]);
        if($cart){
            return $cart['cart_id'];
        }
        return $cart->_add(['cart_id' => $cart]);
    }

    /**
     * 用户类型
     * @param $type int
     * @return array|boolean
     */
    public static function _get_user_type($type = 1){
        switch(intval($type)){
            case self::TYPE_COMMON:
                $_name = '普通用户';
                break;
            case self::TYPE_SELLER:
                $_name = '销售/门店导购';
                break;
            case self::TYPE_DESIGNER:
                $_name = '设计师';
                break;
            case self::TYPE_TAILER:
                $_name = '零售经销商';
                break;
            case self::TYPE_PROJECT:
                $_name = '项目经销商';
                break;
            case self::TYPE_ANZH:
                $_name = '安装商/安装人员';
                break;
            case self::TYPE_EMPLOYEE:
                $_name = '安装商/尚飞员工';
                break;
            default:
                $_name = '普通用户';
                break;
        }
        return $_name;
    }

    /**
     * 用户类型列表
     * @return array|boolean
     */
    public static function _get_user_type_list(){
        return [
            self::TYPE_COMMON =>    '普通用户',
            self::TYPE_SELLER =>    '销售/门店导购',
            self::TYPE_DESIGNER =>  '设计师',
            self::TYPE_TAILER =>    '零售经销商',
            self::TYPE_PROJECT =>   '项目经销商',
            self::TYPE_ANZH =>      '安装商/安装人员',
            self::TYPE_EMPLOYEE =>  '尚飞员工',
        ];
    }

    /**
     * 用户状态
     * @param $status int
     * @return array|boolean
     */
    public static function _get_user_status($status = 1){
        switch(intval($status)){
            case self::NO_DELETE:
                $_name = '启用';
                break;
            case self::IS_DELETE:
                $_name = '禁用';
                break;
            default:
                $_name = '启用';
                break;
        }
        return $_name;
    }

    /**
     * 用户状态列表
     * @return array|boolean
     */
    public static function _get_user_status_list(){
        return [
            self::NO_DELETE => '启用',
            self::IS_DELETE => '禁用',
        ];
    }

    /**
     * 新增用户记录
     *@param $param array
     * @return array|boolean
     */
    public static function _add_user($param){

        //开启session
        $session = Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }

        //验证手机号
        if(empty($param['mobile'])){
            return ['code' => -20001, 'msg' => '手机号不能为空'];
        }
        $pattern = '/^1[3|5|7|8][0-9]{9}$/';
        if(!preg_match($pattern, trim($param['mobile']))){
            return ['code' => -20002, 'msg' => '手机号格式不正确'];
        }
        $mobile = trim($param['mobile']);

        //验证验证码
        $vf_mdl = new VerifyCode();
        $res = $vf_mdl->_get_info(['mobile' => $mobile]);
        if(!$res){
            return ['code' => -20003, 'msg' => '请获取验证码'];
        }
        if(empty($param['verifycode'])){
            return ['code' => -20003, 'msg' => '验证码不能为空'];
        }
        if($res['verify_code'] != intval($param['verifycode'])){
            return ['code' => -20003, 'msg' => '验证码不正确'];
        }
        if($res['update_at'] < time() - $vf_mdl::EXPIRE_TIME ){
            return ['code' => -20003, 'msg' => '验证码已经失效，请重新获取'];
        }

        //验证微信公众号
        if(empty($param['key'])){
            return ['code' => -20004, 'msg' => '微信公众号没有获取到'];
        }
        $auth = (new Session())->_get_info(['key' => $param['key']]);
        if(!$auth){
            return ['code' => -20004, 'msg' => '微信公众号为空'];
        }
        $wechat_openid = $auth['wechat_openid'];
        $nick = $auth['nick'];
        $avatar = $auth['avatar'];


        $u_mdl = new self;
        $a_mdl = new Auth();

        //验证是否已经手机认证
        $user = $u_mdl->_get_info(['mobile' => $mobile]);
        if($user){
            $res = (new Session())->_delete(['key' => $param['key']]);
            return ['code' => 20001, 'msg' => '已经手机认证过了，直接登录', 'data' => ['uid' => $user['uid']]];
        }

        //开启事务
        $transaction = yii::$app->db->beginTransaction();
        try {

            //用户表插入记录
            $u_mdl->mobile = $mobile;
            $u_mdl->wechat_openid = $wechat_openid;
            $u_mdl->nick = $nick;
            $u_mdl->avatar = $avatar;
            if(!$u_mdl->validate()){
                $error = $u_mdl->errors;
                $msg = current($error)[0];//获取错误信息
                return ['code' => -20007, 'msg' => $msg];
            }
            if(!$u_mdl->save()){
                $transaction->rollBack();
                throw new Exception('用户信息保存失败');
            }
            $uid = self::getDb()->getLastInsertID();

            //添加用户购物车
            $res = (new Cart())->_save(['uid' => $uid]);
            if(!$res){
                $transaction->rollBack();
                throw new Exception('购物车添加失败');
            }

            //添加积分更新记录
            $ret = Points::_add_points($uid, Points::POINTS_MOBILEAUTH);
            if($ret['code'] < 0){
                $transaction->rollBack();
                throw new Exception($ret['msg']);
            }

            //认证表插入记录
            $res_a = $a_mdl->_save([
                'uid' => $uid,
                'mobile' => $mobile,
                'wechat_openid' => $wechat_openid,
                'nick' => $nick,
                'avatar' => $avatar,
            ]);
            if(!$res_a){
                $transaction->rollBack();
                throw new Exception('认证信息保存失败');
            }

            //删除session表
            $res_s = (new Session())->_delete(['key' => $param['key']]);
            if($res_s === false){
                $transaction->rollBack();
                throw new Exception('session删除失败');
            }

            //执行
            $transaction->commit();

            return ['code' => 20000, 'msg' => '保存成功！', 'data' => ['uid' => $uid]];

        } catch (Exception $e) {
            $transaction->rollBack();
            return ['code' => -20000, 'msg' => $e->getMessage()];
        }

    }

}
