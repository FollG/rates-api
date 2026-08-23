<?php

declare(strict_types=1);

namespace app\tests\unit\providers;

use PHPUnit\Framework\TestCase;
use app\providers\CoinGateProvider;
use app\components\HttpClient;
use app\services\CurrencyRegistry;
use app\dto\Rates;
use app\exceptions\ProviderException;


final class CoinGateProviderTest extends TestCase
{
    public function testFetchReturnsRates(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->method('get')
            ->willReturn([
                'merchant'=>[
                    'BTC'=>[
                        'USD'=>50000
                    ]
                ]
            ]);

        $provider =
            new CoinGateProvider(
                $client,
                new CurrencyRegistry(),
                'url'
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

    public function testInvalidResponse(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->method('get')
            ->willReturn([]);

        $provider =
            new CoinGateProvider(
                $client,
                new CurrencyRegistry(),
                'url'
            );

        $this->expectException(ProviderException::class);

        $provider->fetch();
    }

    public function testEmptyRatesThrowsException(): void
    {
        $client = $this->createMock(HttpClient::class);

        $client
            ->method('get')
            ->willReturn([
                'merchant'=>[
                    'COINGAYT'=>[
                        'USD'=>100
                    ]
                ]
            ]);

        $provider =
            new CoinGateProvider(
                $client,
                new CurrencyRegistry(),
                'url'
            );

        $this->expectException(ProviderException::class);

        $provider->fetch();
    }
}