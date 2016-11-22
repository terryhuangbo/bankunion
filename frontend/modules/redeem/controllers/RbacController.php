<?php

namespace frontend\modules\redeem\controllers;

use app\base\BaseController;
use Yii;
use yii\web\Controller;


class RbacController extends BaseController
{

    public $layout = 'layout';
    public $enableCsrfValidation = false;
    public $defaulAction = 'init';

    /**
     * 商品列表
     * @return type
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        // add "createPost" permission 添加“创建文章”的权限
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);
//        exit;
        // add "updatePost" permission 添加“更新文章”的权限
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        // add "author" role and give this role the "createPost" permission
        //创建一个“作者”角色，并给它“创建文章”的权限
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        // 添加“admin”角色，给它“更新文章”的权限
        // “作者”角色
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        // 给用户指定角色，1和2是IdentityInterface::getId()返回的ID，就是用户ID。
        $auth->assign($author, 2);
        $auth->assign($admin, 1);
    }

}
