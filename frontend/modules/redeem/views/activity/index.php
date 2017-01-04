<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name='HandheldFriendly' content='True'/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <link rel="stylesheet" href="/css/style.css">
    <title>聚惠银联 嗨翻大东北</title>
    <style>
        body,html{width:100%; height:100%;}
    </style>
</head>
<body>
<div class="bg">
    <!--黑龙江-->
    <a href="/activity/hlj" class="hlj"></a>
    <!--吉林-->
    <a href="/activity/jl" class="jl"></a>
    <!--辽宁-->
    <a href="/activity/ln" class="ln"></a>
    <a id="rule" class="rule"></a>
    <a class="sign"></a>
    <a href="http://view.zuikuapp.com/s/na/1665_836440.html" target="_blank" class="shake"></a>
</div>
<div class="fixBg">
    <div class="ruleDetail">
        <a class="close"></a>
        <div class="content">
            <h2>一步走：签到</h2>
            <p>每日签到一次，将获得1积分</p>
            <p>额外获得积分方法：</p>
            <p>1.将首页分享至朋友圈，可获得1积分</p>
            <p>2.点击查看商户活动细则，可获得3积分</p>
            <p>Tips：每日积分获得上限为5积分</p>
            <p></p>
            <p>活动期间，累计积分可兑换相应的礼品 （送完为止）</p>
            <h2>二步走：记忆碎片</h2>
            <p>活动期间，各个赛区将分别进行对抗赛实时排名，积分最高的用户可获得20元话费充值包一份。</p>
            <p>同一微信ID每天可挑战一次</p>
            <p>每周日进行排名清算，届时所有用户的比分清零</p>
            <h2>三步走：摇一摇</h2>
            <p>活动时间：2016年11月16日－2017年1月20日</p>
            <p>活动期间，每周三、日可进行摇红包活动，1000元红包摇不停！</p>
            <h2>爆点活动-红包炸弹</h2>
            <p>活动时间：2016年12月24日-12月31日每天下午2点和6点，红包炸弹准时开爆！</p>
            <p>活动规则：准点开始抢红包，1500元红包炸翻天，手慢无！</p>
        </div>
    </div>
</div>
</body>
<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/main.js"></script>
<script src="/js/tools.js"></script>
<script src="/js/message.js"></script>
<script>
    $(".sign").on('click', function(){
        $._ajax('/home/sign', {}, 'POST', 'JSON', function(json){
            if(json.code > 0){
                Alert("+1", 1000, function(){
                    window.location.href = '/home/index';
                });
            }else{
                Alert("今天已经签到，不能重复签到", 1000, function(){
                    window.location.href = '/home/index';
                });
                $(".sign").off('click');
            }
        });
    });
</script>
<!--微信分享-->
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '<?php echo $this->context->signPackage["appId"];?>',
        timestamp: <?php echo $this->context->signPackage["timestamp"];?>,
        nonceStr: '<?php echo $this->context->signPackage["nonceStr"];?>',
        signature: '<?php echo $this->context->signPackage["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            "onMenuShareAppMessage",
            "onMenuShareTimeline",
        ]
    });
    wx.ready(function () {
        // 在这里调用 API
        //发送给朋友
        wx.onMenuShareAppMessage({
            title: '聚惠银联，嗨翻大东北', // 分享标题
            desc: '', // 分享描述
            link: '', // 分享链接
            imgUrl: '<?php echo yiiParams('share_img') ?>', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                $._ajax('/home/share', {}, 'POST', 'JSON', function(json){
                    if(json.code > 0){
                        Alert('恭喜获得1点积分', 2000);
                    }
                });
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: '聚惠银联，嗨翻大东北', // 分享标题
            link: '', // 分享链接
            imgUrl: '<?php echo yiiParams('share_img') ?>', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                $._ajax('/home/share', {}, 'POST', 'JSON', function(json){
                    if(json.code > 0){
                        Alert('恭喜获得1点积分', 2000);
                    }
                });
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
</script>
</html>