<?php

$config = require __DIR__ . '/web.php';
$config['components']['cache'] = [
    'class' => yii\caching\ArrayCache::class,
];

return $config;