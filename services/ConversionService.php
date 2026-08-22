<?php

declare(strict_types=1);

namespace app\services;

use app\dto\Rates;
use app\exceptions\ApiException;
use app\dto\ConversionResult;
use app\requests\ConvertRequest;
use DateTimeImmutable;


final readonly class ConversionService
{
    public function __construct(
        private RatesService $ratesService,
        private RateCalculator $calculator,
        private AmountRounder $rounder,
        private CurrencyRegistry $currencyRegistry,
    ) {
    }


    public function convert(
        ConvertRequest $request
    ): ConversionResult {

        $rates = $this->ratesService->getRawRates();


        $from = $this->currencyRegistry->get(
            $request->currencyFrom
        );

        $to = $this->currencyRegistry->get(
            $request->currencyTo
        );


        if (
            $from === null ||
            $to === null
        ) {
            throw new ApiException(
                'Unsupported currency',
                400
            );
        }


        $fromRate = $rates->get(
            $from->code
        );

        $toRate = $rates->get(
            $to->code
        );


        if (
            $fromRate === null ||
            $toRate === null
        ) {
            throw new ApiException(
                'Unsupported currency',
                400
            );
        }

        $converted = $this->calculator->convert(
            (float)$request->value,
            $fromRate,
            $toRate
        );


        return new ConversionResult(
            currencyFrom: $from->code,
            currencyTo: $to->code,
            value: $request->value,
            rate: (string)$this->calculator->calculateRate(
                $fromRate,
                $toRate
            ),
            convertedValue: $this->rounder->round(
                $converted,
                $to
            ),
            fetchedAt: new DateTimeImmutable()
        );
    }
}