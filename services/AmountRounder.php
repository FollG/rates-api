<?php

declare(strict_types=1);

namespace app\services;

use app\domain\Currency;


readonly class AmountRounder
{
    public function round(
        float $value,
        Currency $currency
    ): string {
        return number_format(
            $value,
            $currency->precision,
            '.',
            ''
        );
    }
}