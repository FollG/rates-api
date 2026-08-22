<?php

declare(strict_types=1);

namespace app\services;

use app\domain\Currency;

final class CurrencyRegistry
{
    /**
     * @var Currency[]
     */
    private array $currencies = [];

    public function __construct()
    {
        $config = require dirname(__DIR__) . '/config/currencies.php';

        foreach ($config as $code => $currency) {
            $this->currencies[$code] = new Currency(
                code: $code,
                type: $currency['type'],
                precision: $currency['precision'],
                name: $currency['name'],
            );
        }
    }

    public function get(string $code): ?Currency
    {
        return $this->currencies[
        strtoupper($code)
        ] ?? null;
    }
}