<?php
namespace app\components\url;

use frontend\components\WechatComponent;

class UrlComponent extends  WechatComponent
{
    /**
     * 长地址转短地址
     * @param $longUrl 长地址
     * @return mixed
     */
    public function shortUrl($longUrl)
    {
        $data = [];
        $data['action'] = 'long2short';
        $data['long_url'] = $longUrl;
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=" . parent::getAccessToken());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch))
        {
            echo curl_error($ch);
        }
        curl_close($ch);
        $tmpInfo = json_decode($tmpInfo);
        if($tmpInfo->errcode === 0)
        {
            return $tmpInfo->short_url;
        }
        return $tmpInfo->errmsg;
    }
}