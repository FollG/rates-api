<?php

declare(strict_types=1);


namespace app\tests\fakes;

use app\domain\enum\CurrencyEnum;
use app\dto\Rates;
use app\dto\Rate;
use app\domain\Currency;
use app\providers\interfaces\RateProviderInterface;


final class FakeRateProvider implements RateProviderInterface
{
    public function fetch(): Rates
    {
        return new Rates(
            'USD',
            [
                new Rate(
                    new Currency(
                        'BTC',
                        CurrencyEnum::CRYPTO,
                        8,
                        'Bitcoin'
                    ),
                    50000
                ),

                new Rate(
                    new Currency(
                        'ETH',
                        CurrencyEnum::CRYPTO,
                        8,
                        'Ethereum'
                    ),
                    2500
                )
            ]
        );
    }
}