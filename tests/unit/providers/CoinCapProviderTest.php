<?php

declare(strict_types=1);

namespace app\tests\unit\providers;

use PHPUnit\Framework\TestCase;
use app\providers\CoinCapProvider;
use app\components\HttpClient;
use app\services\CurrencyRegistry;
use app\dto\Rates;
use app\exceptions\ProviderException;


final class CoinCapProviderTest extends TestCase
{
    public function testFetchReturnsRates(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->expects(self::once())
            ->method('get')
            ->willReturn([
                'data'=>[
                    [
                        'symbol'=>'BTC',
                        'rateUsd'=>'50000'
                    ]
                ]
            ]);

        $provider = new CoinCapProvider(
            $client,
            new CurrencyRegistry(),
            'http://lovetestingasffr'
        );

        $result = $provider->fetch();

        self::assertInstanceOf(
            Rates::class,
            $result
        );

        self::assertTrue(
            $result->has('BTC')
        );
    }

    public function testIgnoresUnknownCurrencies(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->method('get')
            ->willReturn([
                'data'=>[
                    [
                        'symbol'=>'GAYCOIN',
                        'rateUsd'=>'100'
                    ]
                ]
            ]);

        $provider = new CoinCapProvider(
            $client,
            new CurrencyRegistry(),
            'url'
        );

        $this->expectException(ProviderException::class);

        $provider->fetch();
    }

    public function testInvalidResponseThrowsException(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->method('get')
            ->willReturn([]);

        $provider = new CoinCapProvider(
            $client,
            new CurrencyRegistry(),
            'url'
        );

        $this->expectException(
            ProviderException::class
        );

        $provider->fetch();
    }
}