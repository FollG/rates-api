<?php

declare(strict_types=1);

namespace app\services;

use app\dto\Rate;
use app\dto\Rates;

readonly class RateCalculator
{
    public function __construct(
        private CommissionService $commission,
    ) {
    }


    /**
     * Возвращает курсы с учетом комиссии
     */
    public function applyCommission(Rates $rates): Rates
    {
        $result = [];

        foreach ($rates->rates as $rate) {

            $result[] = new Rate(
                currency: $rate->currency,
                usdRate:
                $this->commission->apply(
                    $rate->usdRate
                )
            );
        }

        return new Rates(
            base: $rates->base,
            rates: $result
        );
    }

    /**
     * Расчет конвертации
     */
    public function convert(
        float $amount,
        float $fromRate,
        float $toRate
    ): float {

        $usd = $amount * $fromRate;
        $result = $usd / $toRate;

        return $this->commission->apply($result);
    }

    public function calculateRate(
        float $fromRate,
        float $toRate
    ): float {
        return $fromRate / $toRate;
    }
}