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
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thumb_list', 'description'], 'string'],
            [['redeem_pionts', 'goods_status', 'create_at'], 'integer'],
            [['goods_id'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 50],
            [['thumb'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gid' => 'Gid',
            'goods_id' => 'Goods ID',
            'name' => 'Name',
            'thumb' => 'Thumb',
            'thumb_list' => 'Thumb List',
            'description' => 'Description',
            'redeem_pionts' => 'Redeem Pionts',
            'goods_status' => 'Goods Status',
            'create_at' => 'Create At',
        ];
    }



    public function fields()
    {
        return [
            'gid',
            'my_name' => 'name',
            'create_at' => function($m){
                return date('Y-m-d H:i:s', $m->create_at);
            },
            'pionts' => 'redeem_pionts',
            'description' => function($model){
                return $model->description;
            },
        ];

    }

    public function extraFields()
    {
        return [
            'status' => 'goods_status'
        ];
    }


}
