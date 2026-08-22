<?php

declare(strict_types=1);

use app\components\HttpClient;
use app\components\RedisCache;
use app\providers\CoinCapProvider;
use app\providers\CoinGateProvider;
use app\providers\ChainRateProvider;
use app\providers\interfaces\RateProviderInterface;
use app\repositories\CachedRateRepository;
use app\repositories\RateRepository;
use app\repositories\interfaces\RateRepositoryInterface;
use app\services\CommissionService;
use app\services\RateCalculator;
use Predis\Client;

$params = require __DIR__ . '/params.php';

return [
    'id' => 'rates-api',

    'basePath' => dirname(__DIR__),

    'controllerNamespace' => 'app\\controllers',

    'bootstrap' => [
        'log',
    ],

    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET api/v1' => 'api/index',
                'POST api/v1' => 'api/index',
            ],
        ],
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),

            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => [
                        'error',
                        'warning',
                    ],
                ],
            ],
        ],
    ],

    'container' => [
        'definitions' => [
            CommissionService::class => [
                'class' => CommissionService::class,
                '__construct()' => [
                    'commissionRate' => $params['commissionRate'],
                ],
            ],

            RateCalculator::class => [
                'class' => RateCalculator::class,
            ],

            RateProviderInterface::class => function () use ($params) {

                return new ChainRateProvider([
                    Yii::createObject([
                        'class' => CoinGateProvider::class,
                        '__construct()' => [
                            'url' => $params['providers']['coingateUrl'],
                        ],
                    ]),

                    Yii::createObject([
                        'class' => CoinCapProvider::class,
                        '__construct()' => [
                            'url' => $params['providers']['coincapUrl'],
                        ],
                    ]),
                ]);
            },
            HttpClient::class => HttpClient::class,

            RateRepository::class => [
                'class' => RateRepository::class,
            ],

            RateRepositoryInterface::class => [
                'class' => CachedRateRepository::class,
                '__construct()' => [
                    'freshTtl' => $params['freshRatesTtl'],
                    'fallbackTtl' => $params['fallbackRatesTtl'],
                ],
            ],

            RedisCache::class => function () {
                return new RedisCache(
                    new Client([
                        'scheme' => 'tcp',
                        'host' => getenv('REDIS_HOST') ?: 'redis',
                        'port' => (int)(getenv('REDIS_PORT') ?: 6379),
                    ])
                );
            },
        ],
    ],

    'params' => $params,
];