<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use app\exceptions\ApiException;
use app\requests\ConvertRequest;
use PHPUnit\Framework\TestCase;
use app\services\ConversionService;
use app\services\RatesService;
use app\services\RateCalculator;
use app\services\AmountRounder;
use app\services\CurrencyRegistry;


final class ConversionServiceTest extends TestCase
{
    public function testRejectsUnknownCurrency(): void
    {
        $ratesService = $this->createMock(RatesService::class);
        $calculator = $this->createMock(RateCalculator::class);
        $rounder = $this->createMock(AmountRounder::class);
        $registry = $this->createMock(CurrencyRegistry::class);

        $registry
            ->method('get')
            ->willReturn(null);

        $service = new ConversionService(
            $ratesService,
            $calculator,
            $rounder,
            $registry
        );

        $this->expectException(ApiException::class);

        $request = new ConvertRequest(
            'BTC',
            'ETH',
            "1"
        );

        $service->convert($request);
    }
}