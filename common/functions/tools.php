<?php
/**
 * Created by PhpStorm.
 * User: Huangbo
 * Date: 2016/5/30
 * Time: 11:49
 */

/**
 * 获取yii配置
 * @param string $key
 * @return mix
 */
function yiiParams($key) {
    return yii::$app->params[$key];
}

/**
 * 创建url
 * @param array $params
 * @return string
 */
function yiiUrl($params) {
    return yii::$app->urlManager->createUrl($params);
}


/**
 * 获取值
 * @param $data mixed 要判断是否存在的值
 * @param $default mixed 当$data不存在时默认值
 * @param $empty bool true-同时验证$data还不能为空, 默认不验证
 * @return mixed mix
 **/
function _value($data, $default = '', $empty = false)
{
    if ($empty) {
        return !empty($data) ? $data : $default;
    } else {
        return isset($data) ? $data : $default;
    }
}

/**
 * 获取Request参数
 * @param string $key
 * @param bool|array|string $default 当请求的参数不存在时的默认值
 * @return string
 */
function _request($key = '', $default = false) {
    $request = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
    if(empty($key)){
        return $request;
    }
    if(!isset($request[$key])){
        return $default;
    }
    return $request[$key];
}

/**
 * 从对象，数组中获取获取数据
 * @param $array mixed 数组或者对象
 * @param $key array|string 对象的属性，或者数组的键值/索引，以'.'链接或者放入一个数组
 * @param $default string 如果对象或者属性中不存在该值事返回的值
 * @return mixed mix
 **/
function getValue($array, $key, $default = '')
{
    if ($key instanceof \Closure) {
        return $key($array, $default);
    }

    if (is_array($key)) {
        $lastKey = array_pop($key);
        foreach ($key as $keyPart) {
            $array = getValue($array, $keyPart);
        }
        $key = $lastKey;
    }

    if (is_array($array) && array_key_exists($key, $array)) {
        return $array[$key];
    }

    if (($pos = strrpos($key, '.')) !== false) {
        $array = getValue($array, substr($key, 0, $pos), $default);
        $key = substr($key, $pos + 1);
    }

    if (is_object($array)) {
        return $array->$key;
    } elseif (is_array($array)) {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    } else {
        return $default;
    }
}

/**
 * 判断客户端是否为移动端
 * @param nothing
 * @return boolean
 */
function isMobile() {
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');
    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
        CheckSubstrs($mobile_token_list, $useragent);
    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断是否为子字符串
 * @param $substrs string
 * @param $text string
 * @return boolean
 */
function CheckSubstrs($substrs, $text) {
    foreach ($substrs as $substr)
        if (false !== strpos($text, $substr)) {
            return true;
        }
    return false;
}

/**
 * 对象转数组,使用get_object_vars返回对象属性组成的数组
 * @param $obj object
 * @return array
 */
function objectToArray($obj) {
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_array($arr)) {
        return array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

/**
 * 获取差异时间
 * @param int $time 输入的时间戳
 * @return string 差异时间 如‘2分钟以前，3天以前等等’
 */
function getDiffDate($time) {
    $now = time();
    $diff = $now > $time ? $now - $time : $time - $now;
    $d = floor($diff / 3600 / 24);
    $h = floor($diff / 3600);
    $m = floor($diff / 60);
    if ($d > 0) {
        return $d . '天前';
    } else if ($h > 0) {
        return $h . '小时前';
    } else if ($m > 0) {
        return $m . '分钟前';
    } else {
        return '1分钟前';
    }
}

/**
 * 数组转对象
 * @param array $arr
 * @return array|object
 */
function arrayToObject($arr) {
    if (is_array($arr)) {
        return (object)array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

/**
 * 判断远程文件是否存在
 * @param string $url
 * @return boolean
 */
function _check_file_exists($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $result = curl_exec($curl);
    $found = false;
    if ($result !== false) {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}

/**
 * 用于Debug记录日志，路径为根目录下
 * *@param string|array|int $content
 * *@param int $type 1-清空重新输入；0-换行追加追加
 * *@param string $file 文件名字
 * @return boolean
 */
function lg($content, $type = 1, $file = "1.txt"){
    $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
    if($type){
        file_put_contents($path . $file , print_r($content ,true));
    }else{
        file_put_contents($path .$file, "\r\n" . print_r($content ,true), FILE_APPEND);
    }
}

/**
 * 用于分析代码片段执行的时间
 * @return array
 */
function codeProfile() {
    Yii::endProfile('application', 'application');
    //分析结果
    $profiles = Yii::getLogger()->getProfiling(['frontend\*', 'backend\*', 'application'], []);
    //直接体现时间
    $times = yii\helpers\ArrayHelper::map($profiles, 'info', 'duration');
    lg($times, 1, '2.txt');
    lg($profiles, 0, '2.txt');
}

/**
 * 用于分析Application执行的时间
 * @return array
 */
function appProfile() {
    Yii::beginProfile('application', 'application');
}

