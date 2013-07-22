<?php

define('PATH', str_replace('\\', '/', realpath(dirname(__FILE__))));

// change the following paths if necessary
$yii=PATH.'/yii/framework/yii.php';
$config=PATH.'/protected/config/main.php';

// remove the following lines when in production mode
define('YII_DEBUG',true);
define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
