<?php

declare(strict_types=1);

namespace app\tests\integration;

use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidRouteException;
use yii\console\Exception;


abstract class BaseApiTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    protected function request(
        string $httpMethod,
        array $query = [],
        array $body = []
    ): mixed
    {
        $_SERVER['REQUEST_METHOD'] = $httpMethod;
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . Yii::$app->params['apiToken'];

        Yii::$app->request->setQueryParams($query);

        Yii::$app
            ->request
            ->setBodyParams(
                $body
            );

        Yii::$app
            ->request
            ->headers
            ->set(
                'Content-Type',
                'application/json'
            );

        return Yii::$app->runAction(
            'api/index'
        );
    }
}