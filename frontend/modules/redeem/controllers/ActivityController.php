<?php

namespace frontend\modules\redeem\controllers;

use common\behavior\PointBehavior;
use common\lib\Http;
use common\models\Activity;
use common\models\Points;
use common\models\PointsRecord;
use Yii;
use app\base\BaseController;
use yii\base\Exception;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;


class ActivityController extends BaseController
{

    public $layout = 'layout';
    public $enableCsrfValidation = false;

    private $_hlj_id = 10;//黑龙江
    private $_ln_id = 8;//辽宁
    private $_jl_id = 9;//吉林

    /**
     * 关于我们
     * @return type
     */
    public function actionIndex()
    {
        $touser = "oZFzUt4oudWrJwmmaWQ_RvtLSn6I";
        $content = '黄波测试，哈哈哈';
        $APPID = "wxc4c37b00bea02be5";
        $APPSECRET = "f68118d962389fb6f3c6945f3b559244";

        $TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $APPID . "&secret=" . $APPSECRET;

        $json = file_get_contents($TOKEN_URL);
        $result = json_decode($json);

        $ACC_TOKEN = $result->access_token;
        $data = [
            'touser' => $touser,
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ],
        ];


        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $ACC_TOKEN;
        $data = json_encode($data);
        $result = json_decode(Http::_post($url, $data), true);
        lg($ACC_TOKEN);
        return VarDumper::export($result);

//        return $this->render('index');
    }

    /**
     * 黑龙江地区
     * @return type
     */
    public function actionHlj()
    {
        $zone = $this->_hlj_id;
        $class_array = ['mdl', 'dfjzw', 'zz', 'lm', 'wdyc', 'drf', 'wlcs', 'hll', 'lccb', 'zyhcs'];
        $mdl = new Activity();
        $format = [
            'begin_end' => function($m){
                return '开始-' . date('Y-m-d H:i:s', $m->begin_at) . '  截止-' . date('Y-m-d H:i:s', $m->end_at);
            },
            'begin_end1' => function($m){
                return date('Y-m-d', $m->begin_at) . ' - ' . date('Y-m-d', $m->end_at);
            },

        ];
        $format = array_merge($mdl->attributes(), $format);
        $activities = $mdl->getAll(['zone' => $zone, 'status' => Activity::STATUS_ON], 'list_order asc, id desc', 0, 10, $format);
        $_data = [
            'activities' => $activities,
            'class_array' => $class_array,
        ];
        return $this->render('hlj', $_data);
    }

    /**
     * 关于我们
     * @return type
     */
    public function actionLn()
    {

        $zone = $this->_ln_id;
        $class_array = ['hll_ln', 'tmjt', 'drf_ln', 'xtd', 'jy', 'gwzx', 'jyj', 'ysd', 'kdj'];
        $mdl = new Activity();
        $format = [
            'begin_end' => function($m){
                return '开始-' . date('Y-m-d H:i:s', $m->begin_at) . '  截止-' . date('Y-m-d H:i:s', $m->end_at);
            },
            'begin_end1' => function($m){
                return date('Y-m-d', $m->begin_at) . ' - ' . date('Y-m-d', $m->end_at);
            },

        ];
        $format = array_merge($mdl->attributes(), $format);
        $activities = $mdl->getAll(['zone' => $zone, 'status' => Activity::STATUS_ON], 'list_order asc, id desc', 0, 9, $format);
        $_data = [
            'activities' => $activities,
            'class_array' => $class_array,
        ];
        return $this->render('ln', $_data);
    }

    /**
     * 关于我们
     * @return type
     */
    public function actionJl()
    {
        $zone = $this->_jl_id;
        $class_array = ['hll_jl', 'xtdcs', 'drf_jl', 'oyjt', 'ct', 'gpp'];
        $mdl = new Activity();
        $format = [
            'begin_end' => function($m){
                return '开始-' . date('Y-m-d H:i:s', $m->begin_at) . '  截止-' . date('Y-m-d H:i:s', $m->end_at);
            },
            'begin_end1' => function($m){
                return date('Y-m-d', $m->begin_at) . ' - ' . date('Y-m-d', $m->end_at);
            },
        ];
        $format = array_merge($mdl->attributes(), $format);
        $activities = $mdl->getAll(['zone' => $zone, 'status' => Activity::STATUS_ON], 'list_order asc, id desc', 0, 6, $format);
        $_data = [
            'activities' => $activities,
            'class_array' => $class_array,
        ];

        return $this->render('jl', $_data);
    }

    /**
     * 赠送积分
     * @return type
     */
    public function actionPoints()
    {
        $id = intval($this->_request('id'));
        $user = Yii::$app->user->identity;//当前登录用户
        //附属添加积分行为到登录用户
        $user->attachBehavior('signpoints', [
                'class' =>  PointBehavior::className(),
                'points' => 1,
                'point_id' => $id,
                'points_name' => PointsRecord::POINTS_DETAIL,
            ]);
        $user->points += 1;
        $ret = $user->save();
        $this->_json($ret['code'], $ret['msg']);
    }

}
