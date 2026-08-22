<?php

declare(strict_types=1);

namespace app\services;


final readonly class AmountRounder
{

    private array $crypto;

    public function __construct()
    {
        $this->crypto = [
            'BTC',
            'ETH',
            'LTC',
            'XRP',
            'BCH',
            'DOGE',
        ];
    }


    public function round(
        float $value,
        string $currency
    ): string {

        $precision =
            in_array(
                strtoupper($currency),
                $this->crypto,
                true
            )
                ? 10
                : 2;


        return number_format(
            $value,
            $precision,
            '.',
            ''
        );
    }

    public function roundRate(
        float $amount,
        int $precision
    ): string {
        return number_format(
            $amount,
            $precision,
            '.',
            ''
        );
    }

}