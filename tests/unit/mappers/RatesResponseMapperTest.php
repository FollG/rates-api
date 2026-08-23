<?php

declare(strict_types=1);

namespace app\tests\unit\mappers;

use PHPUnit\Framework\TestCase;
use app\mappers\RatesResponseMapper;
use app\dto\Rates;
use app\dto\Rate;
use app\domain\Currency;
use app\domain\enum\CurrencyEnum;

final class RatesResponseMapperTest extends TestCase
{
    public function testMapsRates(): void
    {
        $currency =
            new Currency(
                'BTC',
                CurrencyEnum::CRYPTO,
                2,
                'Bitcoin'
            );

        $rates =
            new Rates(
                'USD',
                [
                    new Rate(
                        $currency,
                        123.456
                    )
                ]
            );

        $mapper = new RatesResponseMapper();
        $response = $mapper->map($rates);

        self::assertEquals(
            [
                'BTC'=>'123.46'
            ],
            $response->data
        );
    }
}