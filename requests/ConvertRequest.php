<?php

declare(strict_types=1);

namespace app\requests;

use InvalidArgumentException;

final readonly class ConvertRequest
{
    public function __construct(
        public string $currencyFrom,
        public string $currencyTo,
        public string $value,
    ) {

        if (
            trim($currencyFrom) === '' ||
            trim($currencyTo) === ''
        ) {
            throw new InvalidArgumentException(
                'Currency required'
            );
        }


        if (!is_numeric($value)) {
            throw new InvalidArgumentException(
                'Amount must be numeric'
            );
        }


        if ((float)$value < 0.01) {
            throw new InvalidArgumentException(
                'Minimum amount is 0.01'
            );
        }
    }
}