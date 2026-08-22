<?php

declare(strict_types=1);

namespace app\services;

use app\dto\ConversionResult;
use app\requests\ConvertRequest;
use app\repositories\interfaces\RateRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;


final readonly class ConversionService
{
    public function __construct(
        private RatesService $ratesService,
        private RateCalculator $calculator,
        private AmountRounder $rounder,
    ) {
    }


    public function convert(
        ConvertRequest $request
    ): ConversionResult {

        $rates = $this->ratesService->getRawRates();
        $from = strtoupper(
            $request->currencyFrom
        );
        $to = strtoupper(
            $request->currencyTo
        );
        $fromRate = $rates->get($from);
        $toRate = $rates->get($to);

        if (
            $fromRate === null ||
            $toRate === null
        ) {
            throw new RuntimeException(
                'Unsupported currency'
            );
        }

        $converted =
            $this->calculator->convert(
                (float)$request->value,
                $fromRate,
                $toRate
            );

        return new ConversionResult(
            currencyFrom: $from,
            currencyTo: $to,
            value: $request->value,
            rate: $this->rounder->roundRate(
                $this->calculator->calculateRate(
                    $fromRate,
                    $toRate
                ),
                10
            ),
            convertedValue: $this->rounder->round(
                $converted,
                $to
            ),
            fetchedAt:
            new DateTimeImmutable()
        );
    }
}