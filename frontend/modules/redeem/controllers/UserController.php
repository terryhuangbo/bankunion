<?php

namespace frontend\modules\redeem\controllers;

use Yii;
use app\base\BaseController;
use common\models\User;

class UserController extends BaseController
{

    public $layout = 'layout';
    public $enableCsrfValidation = false;

    public function init(){
        $this->_uncheck = [
            'login',
            'logout',
        ];
    }

    /**
     * 用户注册
     * @return type
     */
    public function actionLogin()
    {
        //加载
        if(!$this->isAjax()){
            $key = $this->_request('key', '');
            $_data = [
                'key' => $key,
            ];
            return $this->render('login', $_data);
        }

        $ret = (new User())->login(Yii::$app->request->post());
        if ($ret['code'] < 0) {
            $this->_json($ret['code'], $ret['msg']);
        }
        $url = Yii::$app->session->get('_redirectUrl');
        if(empty($url) || Yii::$app->request->absoluteUrl === $url){
            $url = Yii::$app->homeUrl;
        }
        return $this->redirect($url);
    }

    /**
     * 退出登录
     * @return type
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/redeem/user/login');//跳转到登录页
    }

}
