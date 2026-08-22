<?php

declare(strict_types=1);

namespace app\dto;

final readonly class Rate
{
    public function __construct(
        public string $currency,
        public float $usdRate,
    ) {
    }
}