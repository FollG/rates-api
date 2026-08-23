<?php

declare(strict_types=1);

namespace app\components;

use Predis\Client;

readonly class RedisCache
{
    public function __construct(
        private Client $redis,
    ) {
    }


    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);

        return $value === null ? null : $value;
    }


    public function set(
        string $key,
        string $value,
        int $ttl
    ): void {
        $this->redis->setex(
            $key,
            $ttl,
            $value
        );
    }


    public function delete(string $key): void
    {
        $this->redis->del([$key]);
    }
}