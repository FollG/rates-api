<?php

declare(strict_types=1);

namespace app\components;

use yii\base\InvalidConfigException;
use yii\httpclient\Client as YiiHttpClient;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;

class HttpClient
{
    private YiiHttpClient $client;

    public function __construct()
    {
        $this->client = new YiiHttpClient([
            'transport' => [
                'class' => CurlTransport::class,
            ],
        ]);
    }


    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function get(string $url): array
    {
        $response = $this->client
            ->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->send();

        if (!$response->isOk) {
            throw new \RuntimeException(
                'HTTP request failed: ' . $response->statusCode
            );
        }

        return $response->data;
    }
}