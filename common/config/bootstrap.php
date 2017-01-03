<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('environment', dirname(dirname(__DIR__)) . '/environment');
Yii::setAlias('upload', dirname(dirname(__DIR__)) . '/frontend/web/upload/');
//广告生成的js存储地址
Yii::setAlias('bannerJs', dirname(dirname(__DIR__)) . '/frontend/webrc/js/banners');
