<?php

declare(strict_types=1);

namespace app\tests\integration;

use yii\base\InvalidRouteException;
use yii\console\Exception;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;


final class ApiErrorsTest extends BaseApiTest
{
    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testUnknownMethod(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $this->request(
            'GET',
            [
                'method'=>'sosaity'
            ]
        );
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testRatesDoesNotAllowPost(): void
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $this->request(
            'POST',
            [
                'method'=>'rates'
            ]
        );
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testConvertDoesNotAllowGet(): void
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $this->request(
            'GET',
            [
                'method'=>'convert'
            ]
        );
    }
}