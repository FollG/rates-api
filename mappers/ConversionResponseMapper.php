<?php

declare(strict_types=1);

namespace app\mappers;

use app\dto\ApiResponse;
use app\dto\ConversionResult;


final readonly class ConversionResponseMapper
{
    public function map(
        ConversionResult $result
    ): ApiResponse {

        return ApiResponse::success(
            data: [
                'currency_from' => $result->currencyFrom,
                'currency_to' => $result->currencyTo,
                'value' => $result->value,
                'converted_value' => $result->convertedValue,
                'rate' => $result->rate,
            ],
            fetchedAt: $result->fetchedAt
                ->format(DATE_ATOM)
        );
    }
}