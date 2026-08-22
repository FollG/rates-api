<?php

declare(strict_types=1);

namespace app\providers;

use app\components\HttpClient;
use app\dto\Rate;
use app\dto\Rates;
use app\exceptions\ProviderException;
use app\providers\interfaces\RateProviderInterface;
use app\services\CurrencyRegistry;

final class CoinGateProvider extends AbstractRateProvider implements RateProviderInterface
{
    public function __construct(
        private readonly HttpClient $client,
        private readonly CurrencyRegistry $currencyRegistry,
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
                    empty($data['merchant'])
                    || !is_array($data['merchant'])
                ) {
                    throw new ProviderException(
                        'Invalid CoinGate response'
                    );
                }

                return $this->normalize(
                    $data['merchant']
                );
            },
            'CoinGate'
        );
    }


    private function normalize(
        array $merchant
    ): Rates {
        $rates = [];

        foreach ($merchant as $currency => $value) {
            if (
                !isset($value['USD'])
            ) {
                continue;
            }

            $currencyObject = $this->currencyRegistry->get(
                $currency
            );

            if ($currencyObject === null) {
                continue;
            }

            $rates[] = new Rate(
                currency: $currencyObject,
                usdRate: (float) $value['USD']
            );
        }

        if ($rates === []) {
            throw new ProviderException(
                'CoinGate returned empty rates'
            );
        }

        return new Rates(
            base: 'USD',
            rates: $rates
        );
    }
}