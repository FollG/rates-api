<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\Rates;
use app\providers\interfaces\RateProviderInterface;
use app\repositories\interfaces\RateRepositoryInterface;
use Throwable;

final readonly class RateRepository implements RateRepositoryInterface
{
    public function __construct(
        private RateProviderInterface $provider,
    ) {
    }

    public function getRates(): Rates
    {
        return $this->provider->fetch();
    }
}