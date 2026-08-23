<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

defined('YII_DEBUG')
|| define('YII_DEBUG', true);

defined('YII_ENV')
|| define('YII_ENV', 'test');

require dirname(__DIR__, 2) . '/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__, 2) . '/config/test.php';

try {
    new yii\web\Application($config);
} catch (\yii\base\InvalidConfigException $e) {
    var_dump($e->getMessage());exit;
}