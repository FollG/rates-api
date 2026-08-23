<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\CurrencyRegistry;


final class CurrencyRegistryTest extends TestCase
{
    public function testReturnsCurrency(): void
    {
        $registry = new CurrencyRegistry();
        $currency = $registry->get('btc');

        self::assertNotNull($currency);

        self::assertEquals(
            'BTC',
            $currency->code
        );
    }

    public function testUnknownCurrency(): void
    {
        $registry = new CurrencyRegistry();

        self::assertNull(
            $registry->get('GAYCOIN')
        );
    }
}