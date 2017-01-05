<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $gid
 * @property string $goods_id
 * @property string $name
 * @property string $thumb
 * @property string $thumb_list
 * @property string $description
 * @property integer $redeem_pionts
 * @property integer $goods_status
 * @property integer $create_at
 */
class Order extends \common\models\Order
{
    public function fields()
    {
        return [
            'gid',
            'uid',
            'goods_bn' => 'goods_id',
            'goodsname' => 'goods_name',
            'mobile' => function($model, $field){
                return $model->user->mobile;
            },
            'dec' => function($model, $field){
                return $model->goods->description;
            },

        ];
    }

    public function extraFields()
    {
        return [
            'count' => 'count',
            'express_type' => 'express_type',
        ];

    }




}
