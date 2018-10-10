<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../core_framework/vendor/autoload.php');
require(__DIR__ . '/../core_framework/vendor/yiisoft/yii2/Yii.php');
require (__DIR__ . '/../core_framework/config/bootstrap.php');

$config = require(__DIR__ . '/config/api.php');

(new yii\web\Application($config))->run();
