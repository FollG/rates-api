<?php

declare(strict_types=1);

namespace app\dto;


use app\services\CurrencyRegistry;
use InvalidArgumentException;

readonly class Rates
{
    /**
     * @param Rate[] $rates
     */
    public function __construct(
        public string $base,
        public array  $rates,
    )
    {
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

            if ($rate->currency->code === $currency) {
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
            static fn(Rate $a, Rate $b) => $a->usdRate <=> $b->usdRate
        );

        return new self('USD', $rates);
    }

    public static function fromArray(
        array            $data,
        CurrencyRegistry $registry
    ): self
    {
        $rates = array_map(
            function (array $rate) use ($registry): Rate {

                $currency = $registry->get(
                    $rate['currency']
                );

                if ($currency === null) {
                    throw new InvalidArgumentException(
                        "Unknown currency {$rate['currency']}"
                    );
                }

                return new Rate(
                    $currency,
                    (float)$rate['usdRate']
                );
            },
            $data['rates']
        );


        return new self(
            base: $data['base'],
            rates: $rates
        );
    }

    public function toCacheArray(): array
    {
        return [
            'base' => $this->base,
            'rates' => array_map(
                static fn(Rate $rate) => [
                    'currency' => $rate->currency->code,
                    'usdRate' => $rate->usdRate,
                ],
                $this->rates
            ),
        ];
    }

    public function filterByCurrencies(?array $currencies): self
    {
        $currencies = array_map(
            static fn(string $currency): string => strtoupper(trim($currency)),
            (array)$currencies
        );

        $filteredRates = array_filter(
            $this->rates,
            static function (Rate $rate) use ($currencies): bool {
                return in_array(
                    $rate->currency->code,
                    $currencies,
                    true
                );
            }
        );

        return new self(
            base: $this->base,
            rates: array_values($filteredRates)
        );
    }
}