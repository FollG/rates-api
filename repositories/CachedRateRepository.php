<?php

namespace app\repositories;

use app\components\RedisCache;
use app\dto\Rates;
use app\repositories\interfaces\RateRepositoryInterface;
use app\services\CurrencyRegistry;

final readonly class CachedRateRepository implements RateRepositoryInterface
{
    private const FRESH_CACHE_KEY = 'rates:usd:fresh';
    private const FALLBACK_CACHE_KEY = 'rates:usd:last';


    public function __construct(
        private RateRepository $repository,
        private RedisCache $cache,
        private int $freshTtl,
        private int $fallbackTtl,
        private CurrencyRegistry $currencyRegistry,
    ) {
    }

    public function getRates(): Rates
    {
        $fresh = $this->cache->get(
            self::FRESH_CACHE_KEY
        );

        if ($fresh !== null) {
            return Rates::fromArray(
                json_decode($fresh, true),
                $this->currencyRegistry
            );
        }

        try {
            $rates = $this->repository->getRates();
            $serialized = json_encode(
                $rates->toCacheArray()
            );
            $this->cache->set(
                self::FRESH_CACHE_KEY,
                $serialized,
                $this->freshTtl
            );
            $this->cache->set(
                self::FALLBACK_CACHE_KEY,
                $serialized,
                $this->fallbackTtl
            );
            return $rates;
        } catch (\Throwable $e) {
            $fallback = $this->cache->get(
                self::FALLBACK_CACHE_KEY
            );
            if ($fallback !== null) {
                return Rates::fromArray(
                    json_decode($fresh, true),
                    $this->currencyRegistry
                );
            }
            throw $e;
        }
    }
}