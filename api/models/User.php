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
class User extends \common\models\User
{
    public function fields()
    {
        return [
            'uid',
            'mobile',
            'fens' => 'points',
        ];
    }

    public function extraFields()
    {
        return [
            'usertype' => 'user_type',
            'userstatus' => 'user_status',
        ];

    }




}
