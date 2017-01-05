<?php

namespace api\modules\v1\controllers;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * 实现自己定义的操作
 * @package api\modules\v1\controllers
 */
class SiteController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\Goods';

    /**
     * @var array
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @return array
     */
    public function actions()
    {
        $actions =  parent::actions();
        //注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create']);
        return $actions;
    }


    /**
     * http://api.bankunion.com/v1/sites 注意，这里是复数sites
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find()->where(['>', 'gid', 10]);//这里必须是QueryInterface
        return new ActiveDataProvider([
            'query' => $query,
            'key' => 'gid',
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new $this->modelClass();
        // $model->load(Yii::$app->getRequest()
        // ->getBodyParams(), '');
        $model->attributes = Yii::$app->request->post();

        if (!$model->save(true)) {
            return reset($model->getFirstErrors());
        }
        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->getQueryParams();//获取GET，PATCH的参数
        if (!$model->save())
        {
            return reset($model->getFirstErrors());
        }
        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->findModel($id)->delete();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 检查用户能否访问 $action 和 $model
        // 访问被拒绝应抛出ForbiddenHttpException
        // var_dump($params);exit;
    }


}
