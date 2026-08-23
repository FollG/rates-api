<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use app\domain\Currency;
use app\domain\enum\CurrencyEnum;
use app\services\AmountRounder;
use PHPUnit\Framework\TestCase;


final class AmountRounderTest extends TestCase
{
    public function testRoundsAmountAccordingToCurrencyPrecision(): void
    {
        $currency = new Currency(
            'BTC',
            CurrencyEnum::CRYPTO,
            8,
            'Bitcoin'
        );

        $rounder = new AmountRounder();

        $result = $rounder->round(
            1.123456789,
            $currency
        );

        self::assertEquals(
            '1.12345679',
            $result
        );
    }
}