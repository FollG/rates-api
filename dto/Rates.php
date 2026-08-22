<?php

declare(strict_types=1);

namespace app\dto;


use InvalidArgumentException;
use JsonSerializable;

final readonly class Rates
{
    /**
     * @param Rate[] $rates
     */
    public function __construct(
        public string $base,
        public array $rates,
    ) {
        foreach ($this->rates as $rate) {

            if (!$rate instanceof Rate) {
                throw new InvalidArgumentException(
                    'Rates collection must contain only Rate objects'
                );
            }
        }
    }


    /**
     * @return Rate[]
     */
    public function all(): array
    {
        return $this->rates;
    }


    public function get(string $currency): ?float
    {
        $currency =
            strtoupper($currency);


        foreach ($this->rates as $rate) {

            if ($rate->currency === $currency) {
                return $rate->usdRate;
            }
        }


        return null;
    }


    public function has(string $currency): bool
    {
        return $this->get($currency) !== null;
    }

    public function sortByRate(): self
    {
        $rates = $this->rates;

        usort(
            $rates,
            static fn (Rate $a, Rate $b) =>
                $a->usdRate <=> $b->usdRate
        );

        return new self('USD', $rates);
    }

    public static function fromArray(array $data): self
    {
        $rates = array_map(
            static function (array $rate): Rate {
                return new Rate(
                    $rate['currency'],
                    $rate['usdRate']
                );
            },
            $data['rates']
        );

        return new self(
            $data['base'],
            $rates
        );
    }

    public function toCacheArray(): array
    {
        return [
            'base' => $this->base,
            'rates' => array_map(
                static fn(Rate $rate) => [
                    'currency' => $rate->currency,
                    'usdRate' => $rate->usdRate,
                ],
                $this->rates
            ),
        ];
    }
}