<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__) . '/config/web.php';

try {
    new yii\web\Application($config);
} catch (\yii\base\InvalidConfigException $e) {
    var_dump($e->getMessage());exit;
}