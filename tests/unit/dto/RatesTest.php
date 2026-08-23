<?php

declare(strict_types=1);

namespace app\tests\unit\dto;

use PHPUnit\Framework\TestCase;
use app\dto\Rate;
use app\dto\Rates;
use app\domain\Currency;
use app\domain\enum\CurrencyEnum;
use InvalidArgumentException;


final class RatesTest extends TestCase
{
    private function btc(): Currency
    {
        return new Currency(
            code: 'BTC',
            type: CurrencyEnum::CRYPTO,
            precision: 8,
            name: 'Bitcoin'
        );
    }

    private function eth(): Currency
    {
        return new Currency(
            code: 'ETH',
            type: CurrencyEnum::CRYPTO,
            precision: 8,
            name: 'Ethereum'
        );
    }

    public function testGetCurrencyRate(): void
    {
        $rates = new Rates(
            'USD',
            [
                new Rate(
                    $this->btc(),
                    50000
                )
            ]
        );

        self::assertEquals(
            50000,
            $rates->get('btc')
        );
    }

    public function testUnknownCurrencyReturnsNull(): void
    {
        $rates = new Rates(
            'USD',
            []
        );

        self::assertNull(
            $rates->get('BTC')
        );
    }

    public function testHasCurrency(): void
    {
        $rates = new Rates(
            'USD',
            [
                new Rate(
                    $this->btc(),
                    50000
                )
            ]
        );

        self::assertTrue(
            $rates->has('BTC')
        );

        self::assertFalse(
            $rates->has('ETH')
        );
    }

    public function testSortByRate(): void
    {
        $rates = new Rates(
            'USD',
            [
                new Rate(
                    $this->btc(),
                    50000
                ),
                new Rate(
                    $this->eth(),
                    2500
                )
            ]
        );

        $sorted = $rates->sortByRate();

        self::assertEquals(
            'ETH',
            $sorted->all()[0]->currency->code
        );
    }

    public function testInvalidRateObjectThrowsException(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new Rates(
            'USD',
            [
                []
            ]
        );
    }

    public function testFilterCurrencies(): void
    {
        $rates = new Rates(
            'USD',
            [
                new Rate(
                    $this->btc(),
                    50000
                ),
                new Rate(
                    $this->eth(),
                    2500
                )
            ]
        );

        $filtered = $rates->filterByCurrencies(['btc']);

        self::assertCount(
            1,
            $filtered->all()
        );

        self::assertEquals(
            'BTC',
            $filtered->all()[0]->currency->code
        );
    }

    public function testToCacheArray(): void
    {
        $rates = new Rates(
            'USD',
            [
                new Rate(
                    $this->btc(),
                    50000
                )
            ]
        );

        self::assertEquals(
            [
                'base' => 'USD',
                'rates' => [
                    [
                        'currency' => 'BTC',
                        'usdRate' => 50000
                    ]
                ]
            ],
            $rates->toCacheArray()
        );
    }
}