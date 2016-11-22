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
     * 初始化，创建权限数据
     * @return type
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        /*创建权限*/
        // add "createPost" permission 添加“创建文章”的权限
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);
        // add "updatePost" permission 添加“更新文章”的权限
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        /*创建角色，并且分配这个角色权限*/
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

        /*创建额外的规则，并将规则赋予权限*/
        // add the rule
        //添加一条规则
        $rule = new \app\rbac\rules\AuthorRule;
        $auth->add($rule);
        // add the "updateOwnPost" permission and associate the rule with it.
        //添加“updateOwnPost”权限，并且和上面的规则关联起来
        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $rule->name;//将规则赋予权限
        $auth->add($updateOwnPost);//将Permission添加到系统当中
        // "updateOwnPost" will be used from "updatePost"
        $auth->addChild($updateOwnPost, $updatePost);
        // allow "author" to update their own posts
        $auth->addChild($author, $updateOwnPost);

        /*将角色指派给用户*/
        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        // 给用户指定角色，1和2是IdentityInterface::getId()返回的ID，就是用户ID。
        $auth->assign($author, 2);
        $auth->assign($admin, 1);

    }

    /**
     * 使用默认角色，创建权限数据，
     * 用户表的一个字段(比如权限组group)必须映射到RBAC的一个角色
     *       'components' => [
     *           'authManager' => [
     *               'class' => 'yii\rbac\PhpManager',
     *               'defaultRoles' => ['admin', 'author'],
     *            ],
     *           // ...
     *       ],
     * @return type
     */
    public function actionInit1()
    {
        $auth = Yii::$app->authManager;
        $rule = new \app\rbac\rules\UserGroupRule;
        $auth->add($rule);

        $author = $auth->createRole('author');
        $author->ruleName = $rule->name;
        $auth->add($author);
        // ... add permissions as children of $author ...

        $admin = $auth->createRole('admin');
        $admin->ruleName = $rule->name;
        $auth->add($admin);
        $auth->addChild($admin, $author);
        // ... add permissions as children of $admin ...

    }

    /**
     * 需要在登录的时候做指派
     * @return type
     */
    public function actionAssign()
    {

        $user = Yii::$app->user->identity;
        $auth = Yii::$app->authManager;
        $authRole = $auth->getRole('author');//admin，指派登录用户特定的角色
        $auth->assign($authRole, $user->getId());
    }

    /**
     * 权限校验
     * @return type
     */
    public function actionCan()
    {

        $param = [
            'post' => [
                'uid' => 2432
            ]
        ];
        $auth = Yii::$app->authManager;
        //使用方法一
        $auth->checkAccess(1, 'author');
        //使用方法二（常用）
        if (Yii::$app->user->can('updateOwnPost', $param, false)) {//或者是createPost，admin，author
            echo '哈哈！我有权限！';
        }else{
            echo '糟糕！我没有权限！';
        }
    }



}
