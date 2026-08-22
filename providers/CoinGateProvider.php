<?php

namespace app\providers;

use app\components\HttpClient;
use app\dto\Rate;
use app\dto\Rates;
use app\exceptions\ProviderException;
use app\providers\interfaces\RateProviderInterface;
use RuntimeException;

class CoinGateProvider extends AbstractRateProvider
{
    public function __construct(
        private readonly HttpClient $client,
        private readonly string     $url,
    ) {
    }


    public function fetch(): Rates
    {
        try {

            $response = $this->client->get(
                $this->url
            );

            if (!isset($response['merchant']) || empty($response)) {
                throw new ProviderException(
                    'Invalid CoinGate response'
                );
            }


            return $this->normalize(
                $response['merchant']
            );


        } catch (\Throwable $e) {

            throw new ProviderException(
                'CoinGate unavailable',
                0,
                $e
            );
        }
    }


    private function normalize(array $merchant): Rates
    {
        $result = [];


        foreach ($merchant as $currency => $value) {

            $result[] = new Rate(
                currency: strtoupper($currency),
                usdRate: (float) $value['USD']
            );
        }


        return new Rates(
            base: 'USD',
            rates: $result
        );
    }
}