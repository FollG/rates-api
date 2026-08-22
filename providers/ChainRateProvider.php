<?php

declare(strict_types=1);

namespace app\providers;

use app\dto\Rates;
use app\exceptions\ProviderException;
use app\providers\interfaces\RateProviderInterface;
use RuntimeException;
use Throwable;


final readonly class ChainRateProvider implements RateProviderInterface
{
    /**
     * @param RateProviderInterface[] $providers
     */
    public function __construct(
        private array $providers,
    ) {
    }


    /**
     * @throws Throwable
     */
    public function fetch(): Rates
    {
        $lastException = null;

        foreach ($this->providers as $provider) {
            try {
                return $provider->fetch();
            } catch (Throwable $e) {
                $lastException = $e;
            }
        }

        throw new ProviderException(
            'All rate providers unavailable',
            previous: $lastException
        );

    }
}