<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('VENDOR_DIR') or define('VENDOR_DIR', __DIR__ . '/../../../../vendor');

require_once(VENDOR_DIR . '/autoload.php');
require_once(VENDOR_DIR . '/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', dirname(__DIR__));

$config = require(dirname(__FILE__) . '/../../config/acceptance.php');
(new yii\console\Application($config));