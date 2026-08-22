<?php

declare(strict_types=1);

namespace app\requests;

use app\exceptions\ValidationException;


final readonly class ConvertRequest
{
    public function __construct(
        public string $currencyFrom,
        public string $currencyTo,
        public string $value,
    ) {
    }


    public static function fromArray(
        array $data
    ): self {

        $currencyFrom = strtoupper(
            trim($data['currency_from'] ?? '')
        );

        $currencyTo = strtoupper(
            trim($data['currency_to'] ?? '')
        );

        $value = $data['value'] ?? null;


        if ($currencyFrom === '') {
            throw new ValidationException(
                'currency_from is required'
            );
        }


        if ($currencyTo === '') {
            throw new ValidationException(
                'currency_to is required'
            );
        }


        if (!is_numeric($value)) {
            throw new ValidationException(
                'value must be numeric'
            );
        }


        if ((float)$value < 0.01) {
            throw new ValidationException(
                'Minimum exchange amount is 0.01'
            );
        }

        if ($currencyFrom === $currencyTo) {
            throw new ValidationException(
                'Currencies must be different'
            );
        }

        return new self(
            currencyFrom: $currencyFrom,
            currencyTo: $currencyTo,
            value: (string)$value,
        );
    }
}