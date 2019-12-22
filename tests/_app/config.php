<?php
return [
    'id' => 'yii2-test-application',
    'basePath' => PATH_ROOT . '/tests/_app',
    'timeZone' => 'Asia/Kuala_Lumpur',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@paw' => dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src',
    ],
];