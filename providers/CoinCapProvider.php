<?php

declare(strict_types=1);

namespace app\providers;

use app\components\HttpClient;
use app\dto\Rate;
use app\dto\Rates;
use app\exceptions\ProviderException;
use app\providers\interfaces\RateProviderInterface;

final class CoinCapProvider extends AbstractRateProvider implements RateProviderInterface
{
    public function __construct(
        private readonly HttpClient $client,
        private readonly string $url,
    ) {
    }


    public function fetch(): Rates
    {
        return $this->execute(
            function (): Rates {
                $data = $this->client->get(
                    $this->url
                );

                if (
                    empty($data['data'])
                    || !is_array($data['data'])
                ) {
                    throw new ProviderException(
                        'Invalid CoinCap response'
                    );
                }

                return $this->normalize(
                    $data['data']
                );
            },
            'CoinCap'
        );
    }

    private function normalize(array $items): Rates
    {
        $rates = [];

        foreach ($items as $item) {

            if (
                !isset(
                    $item['symbol'],
                    $item['rateUsd']
                )
            ) {
                continue;
            }

            $rates[] = new Rate(
                currency: strtoupper($item['symbol']),
                usdRate: (float)$item['rateUsd']
            );
        }

        if ($rates === []) {
            throw new ProviderException(
                'CoinCap returned empty rates'
            );
        }

        return new Rates(
            base: 'USD',
            rates: $rates
        );
    }
}