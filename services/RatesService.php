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


    public function getRatesWithCommission(?string $currencies = null): Rates
    {
        $rates = $this->calculator
            ->applyCommission($this->repository->getRates())
            ->sortByRate();

        if ($currencies === null || $currencies === '') {
            return $rates;
        }

        return $rates->filterByCurrencies(
            explode(',', $currencies)
        );
    }

    public function getRawRates(): Rates
    {
        return $this->repository
            ->getRates();
    }
}