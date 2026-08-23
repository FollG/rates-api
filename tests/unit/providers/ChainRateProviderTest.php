<?php

declare(strict_types=1);

namespace app\tests\unit\providers;

use app\exceptions\ProviderException;
use PHPUnit\Framework\TestCase;
use app\providers\ChainRateProvider;
use app\providers\interfaces\RateProviderInterface;
use app\dto\Rates;


final class ChainRateProviderTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function testReturnsFirstSuccessfulProvider(): void
    {
        $first = $this->createMock(RateProviderInterface::class);
        $second = $this->createMock(RateProviderInterface::class);

        $rates =
            new Rates(
                'USD',
                []
            );

        $first
            ->method('fetch')
            ->willThrowException(
                new \RuntimeException()
            );

        $second
            ->method('fetch')
            ->willReturn(
                $rates
            );

        $service =
            new ChainRateProvider(
                [
                    $first,
                    $second
                ]
            );

        self::assertSame(
            $rates,
            $service->fetch()
        );

    }

    /**
     * @throws \Throwable
     */
    public function testThrowsWhenAllProvidersFail(): void
    {
        $provider = $this->createMock(RateProviderInterface::class);

        $provider
            ->method('fetch')
            ->willThrowException(
                new \RuntimeException()
            );

        $this->expectException(
           ProviderException::class
        );

        (new ChainRateProvider(
            [$provider]
        ))->fetch();
    }
}