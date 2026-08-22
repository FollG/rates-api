<?php

declare(strict_types=1);

namespace app\providers;

use app\exceptions\ProviderException;
use Throwable;

abstract class AbstractRateProvider
{
    /**
     * @throws ProviderException
     */
    protected function execute(
        callable $callback,
        string $providerName
    ): mixed {
        try {
            return $callback();
        } catch (ProviderException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ProviderException(
                "{$providerName} unavailable",
                0,
                $e
            );
        }
    }
}