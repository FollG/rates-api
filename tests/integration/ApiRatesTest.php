<?php

declare(strict_types=1);

namespace app\tests\integration;

use yii\base\InvalidRouteException;
use yii\console\Exception;


final class ApiRatesTest extends BaseApiTest
{
    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testRatesEndpointReturnsSuccess(): void
    {
        $result = $this->request(
            'GET',
            [
                'method' => 'rates'
            ]
        );

        $json = $result->jsonSerialize();

        self::assertEquals(
            'success',
            $json['status']
        );

        self::assertEquals(
            200,
            $json['code']
        );

        self::assertArrayHasKey(
            'fetched_at',
            $json
        );

        self::assertNotEmpty(
            $json['data']
        );
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testRatesFilterWorks(): void
    {
        $result = $this->request(
            'GET',
            [
                'method' => 'rates',
                'currency' => 'BTC'
            ]
        );

        $json = $result->jsonSerialize();

        self::assertEquals(
            'success',
            $json['status']
        );

        self::assertCount(
            1,
            $json['data']
        );

        self::assertArrayHasKey(
            'BTC',
            $json['data']
        );

        self::assertIsString(
            $json['data']['BTC']
        );
    }
}