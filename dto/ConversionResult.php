<?php

declare(strict_types=1);

namespace app\dto;

use DateTimeImmutable;

final readonly class ConversionResult
{
    public function __construct(
        public string $currencyFrom,
        public string $currencyTo,
        public string $value,
        public string $rate,
        public string $convertedValue,
        public DateTimeImmutable $fetchedAt,
    ) {
    }
}