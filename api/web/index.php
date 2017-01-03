<?php
/**
 * Created by PhpStorm.
 * User: Huangbo
 * Date: 2015/5/17
 * Time: 16:22
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

//引入公用函数
require_once(__DIR__.'/../../common/functions/tools.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);
error_reporting(E_ERROR);
$application = new yii\web\Application($config);
if(YII_ENV_DEV && YII_DEBUG)
{
    $application->on('beforeRequest', 'appProfile');
    $application->on('afterRequest', 'codeProfile');
}
$application->run();

