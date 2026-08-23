<?php

declare(strict_types=1);

namespace app\dto;

use app\domain\Currency;

final readonly class Rate
{
    public function __construct(
        public Currency $currency,
        public float $usdRate,
    ) {
    }
}