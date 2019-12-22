<?php
define('PATH_ROOT', dirname(__DIR__));
define('PATH_VENDOR', PATH_ROOT . '/vendor');

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once PATH_VENDOR . '/autoload.php';
require_once PATH_VENDOR . '/yiisoft/yii2/Yii.php';