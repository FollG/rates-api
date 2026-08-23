<?php

declare(strict_types=1);

namespace app\tests\integration;


use app\exceptions\ValidationException;
use yii\base\InvalidRouteException;
use yii\console\Exception;

final class ApiConvertTest extends BaseApiTest
{
    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testConvertWorks(): void
    {
        $result = $this->request(
            'POST',
            [
                'method'=>'convert'
            ],
            [
                'currency_from'=>'BTC',
                'currency_to'=>'ETH',
                'value'=>1
            ]
        );

        $json = $result->jsonSerialize();

        self::assertEquals(
            'success',
            $json['status']
        );

        self::assertEquals(
            'BTC',
            $json['data']['currency_from']
        );

        self::assertEquals(
            'ETH',
            $json['data']['currency_to']
        );
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function testConvertRequiresValue(): void
    {
        $this->expectException(ValidationException::class);

        $this->request(
            'POST',
            [
                'method'=>'convert'
            ],
            [
                'currency_from'=>'BTC',
                'currency_to'=>'ETH'
            ]
        );
    }
}