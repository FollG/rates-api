<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\RatesService;
use app\services\RateCalculator;
use app\services\CommissionService;
use app\repositories\interfaces\RateRepositoryInterface;
use app\dto\Rates;


final class RatesServiceTest extends TestCase
{
    public function testReturnsRawRates(): void
    {
        $repository = $this->createMock(RateRepositoryInterface::class);
        $rates = $this->createMock(Rates::class);

        $repository
            ->expects(self::once())
            ->method('getRates')
            ->willReturn($rates);

        $service = new RatesService(
            $repository,
            new RateCalculator(
                new CommissionService('0.98')
            )
        );

        self::assertSame(
            $rates,
            $service->getRawRates()
        );
    }
}