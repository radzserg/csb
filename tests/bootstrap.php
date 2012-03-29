<?php

$appPath = realpath(dirname(__FILE__) . '/../../../');
$config = $appPath . '/modules/csb/config/test.php';

require_once($appPath . '/yii/framework/yiit.php');
//require_once(dirname(__FILE__).'/WebTestCase.php');

$session = new CHttpSession();
$session->open();

Yii::createWebApplication($config);
