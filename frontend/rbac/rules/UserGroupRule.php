<?php
namespace app\rbac\rules;

use yii\rbac\Rule;
use Yii;

/**
 *   默认角色就是隐含的分配给所有用户的一个角色。调用yii\rbac\ManagerInterface::assign()不是必须的，并且鉴权数据不包含分配信息。
 *   一个默认角色通常关联了一条用来确定角色是否适用于已经检查过了的用户的规则。默认角色常常在已经有角色分配的应用里面使用。
 *   比如，一个应用的用户表有一个用来描述每个用户所属权限组的“group”列，假如每个权限组都可以映射到RBAC的一个角色，
 *   你可以用默认角色的特性自动的把每个用户分配给一个RBAC角色。让我们用一个例子来展示怎样做。
 *   假设在用户表里面，有一个group列，用1来代表管理员（administrator）组，2代码作者（author）组。
 *   你打算用两个RBAC角色admin和author来分别代表上面两个组的权限。
 *   你可以像下面那样创建RBAC数据：
 * return [
 *       // ...
 *       'components' => [
 *            'authManager' => [
 *                'class' => 'yii\rbac\PhpManager',
 *                'defaultRoles' => ['admin', 'author'],
 *           ],
 *       // ...
 *       ],
 *   ];
 */
class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $group = Yii::$app->user->identity->group;//group必须和默认角色admin，author等一一对应
            if ($item->name === 'admin') {
                return $group == 1;
            } elseif ($item->name === 'author') {
                return $group == 1 || $group == 2;//必须考虑到admin，author的父子级关系
            }
        }
        return false;
    }
}