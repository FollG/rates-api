<?php

declare(strict_types=1);

namespace app\domain;


use app\domain\enum\CurrencyEnum;

final readonly class Currency
{
    public function __construct(
        public string $code,
        public CurrencyEnum $type,
        public int $precision,
        public string $name,
    ) {
    }
}