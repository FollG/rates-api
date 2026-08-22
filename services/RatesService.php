<?php

declare(strict_types=1);

namespace app\services;

use app\dto\Rates;
use app\repositories\interfaces\RateRepositoryInterface;


final readonly class RatesService
{
    public function __construct(
        private RateRepositoryInterface $repository,
        private RateCalculator $calculator,
    ) {
    }


    public function getRatesWithCommission(): Rates
    {
        return $this->calculator
            ->applyCommission($this->repository->getRates())
            ->sortByRate();
    }

    public function getRawRates(): Rates
    {
        return $this->repository
            ->getRates();
    }

    private function filterCurrencies(array $rates, ?string $currency): array
    {
        if ($currency === null || $currency === '') {
            return $rates;
        }

        $requestedCurrencies = array_map(
            'trim',
            explode(',', strtoupper($currency))
        );

        return array_filter(
            $rates,
            static function ($rate) use ($requestedCurrencies) {
                return in_array(
                    $rate->getCurrency(),
                    $requestedCurrencies,
                    true
                );
            }
        );
    }
}