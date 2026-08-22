<?php

declare(strict_types=1);

namespace app\services;

final readonly class CommissionService
{
    public function __construct(
        private string $commissionRate
    ) {
    }


    public function apply(float $amount): float
    {
        return $amount * (float)$this->commissionRate;
    }
}