<?php

declare(strict_types=1);

namespace app\mappers;

use app\dto\ApiResponse;
use app\dto\Rates;
use DateTimeImmutable;


final readonly class RatesResponseMapper
{
    public function map(
        Rates $rates,
    ): ApiResponse {

        return ApiResponse::success(
            data: $this->normalize($rates),
            fetchedAt: (new DateTimeImmutable())
                ->format(DATE_ATOM)
        );
    }


    private function normalize(
        Rates $rates
    ): array {

        $result = [];

        foreach ($rates->all() as $rate) {
            $result[$rate->currency->code] =
                number_format(
                    $rate->usdRate,
                    $rate->currency->precision,
                    '.',
                    ''
                );
        }

        return $result;
    }
}