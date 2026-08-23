<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use app\services\CommissionService;
use app\services\RateCalculator;
use PHPUnit\Framework\TestCase;


final class RateCalculatorTest extends TestCase
{
    private function calculator(): RateCalculator
    {
        return new RateCalculator(new CommissionService('0.98'));
    }

    public function testConvert(): void
    {
        $calculator = $this->calculator();

        /*
         * 100 BTC
         * BTC = 50000 USD
         * ETH = 2500 USD
         *
         * USD:
         * 100 * 50000 = 5000000
         *
         * ETH:
         * 5000000 / 2500 = 2000
         *
         * комиссия:
         * 2000 * 0.98 = 1960
         */
        $result = $calculator->convert(
            100,
            50000,
            2500
        );

        self::assertEquals(
            1960,
            $result
        );
    }


    public function testCalculateRate(): void
    {
        $calculator = $this->calculator();

        self::assertEquals(
            20,
            $calculator->calculateRate(
                50000,
                2500
            )
        );
    }
}