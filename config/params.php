<?php

declare(strict_types=1);

return [
    'apiToken' => (string) getenv('API_TOKEN'),
    'requestTimeout' => (int) (getenv('RATES_HTTP_TIMEOUT') ?: 3),
    'freshRatesTtl' => (int) (getenv('RATES_FRESH_TTL') ?: 30),
    'fallbackRatesTtl' => (int) (getenv('RATES_FALLBACK_TTL') ?: 3600),
    'rateLimit' => (int) (getenv('RATE_LIMIT_REQUESTS') ?: 60),
    'rateLimitWindow' => (int) (getenv('RATE_LIMIT_WINDOW') ?: 60),
    'commissionRate' => '0.98',
    'providers' => [
        'coingateUrl' => (string) (getenv('COINGATE_URL') ?: 'https://api.coingate.com/api/v2/rates'),
        'coincapUrl' => (string) (getenv('COINCAP_URL') ?: 'https://api.coincap.io/v2/rates'),
    ],
];
