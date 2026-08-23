<?php

declare(strict_types=1);

namespace app\tests\unit\repositories;

use PHPUnit\Framework\TestCase;
use app\repositories\CachedRateRepository;
use app\repositories\RateRepository;
use app\components\RedisCache;
use app\services\CurrencyRegistry;
use app\dto\Rates;
use RuntimeException;
use Throwable;


final class CachedRateRepositoryTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testReturnsFreshCacheWithoutCallingRepository(): void
    {
        $cache = $this->createMock(RedisCache::class);
        $repository = $this->createMock(RateRepository::class);
        $currencyRegistry = new CurrencyRegistry();

        $cache
            ->expects(self::once())
            ->method('get')
            ->with('rates:usd:fresh')
            ->willReturn(
                json_encode([
                    'base'=>'USD',
                    'rates'=>[]
                ])
            );

        $repository
            ->expects(self::never())
            ->method('getRates');

        $service = new CachedRateRepository(
            $repository,
            $cache,
            60,
            3600,
            $currencyRegistry
        );

        $result = $service->getRates();

        self::assertInstanceOf(
            Rates::class,
            $result
        );
    }

    /**
     * @throws Throwable
     */
    public function testLoadsFromRepositoryAndCachesResult(): void
    {
        $cache = $this->createMock(RedisCache::class);
        $repository = $this->createMock(RateRepository::class);

        $rates =
            new Rates(
                'USD',
                []
            );

        $repository
            ->expects(self::once())
            ->method('getRates')
            ->willReturn($rates);

        $cache
            ->expects(self::atLeastOnce())
            ->method('set');

        $cache
            ->method('get')
            ->willReturn(null);

        $service =
            new CachedRateRepository(
                $repository,
                $cache,
                60,
                3600,
                new CurrencyRegistry()
            );

        self::assertSame(
            $rates,
            $service->getRates()
        );
    }

    /**
     * @throws Throwable
     */
    public function testUsesFallbackCacheWhenProviderFails(): void
    {
        $cache = $this->createMock(RedisCache::class);
        $repository = $this->createMock(RateRepository::class);

        $repository
            ->method('getRates')
            ->willThrowException(
                new \RuntimeException()
            );

        $cache
            ->method('get')
            ->willReturnCallback(
                function(string $key){
                    if ($key === 'rates:usd:fresh') {
                        return null;
                    }

                    if ($key === 'rates:usd:last') {
                        return json_encode([
                            'base'=>'USD',
                            'rates'=>[]
                        ]);
                    }

                    return null;
                }
            );

        $service =
            new CachedRateRepository(
                $repository,
                $cache,
                60,
                3600,
                new CurrencyRegistry()
            );

        $result = $service->getRates();

        self::assertInstanceOf(
            Rates::class,
            $result
        );
    }

    public function testThrowsExceptionWithoutFallback(): void
    {
        $this->expectException(Throwable::class);
        $cache = $this->createMock(RedisCache::class);
        $repository = $this->createMock(RateRepository::class);

        $repository
            ->method('getRates')
            ->willThrowException(
                new RuntimeException()
            );

        $cache
            ->method('get')
            ->willReturn(null);

        $service =
            new CachedRateRepository(
                $repository,
                $cache,
                60,
                3600,
                new CurrencyRegistry()
            );

        $service->getRates();
    }
}