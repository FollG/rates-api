<?php

declare(strict_types=1);

namespace app\providers\interfaces;

use app\dto\Rates;

interface RateProviderInterface
{
    public function fetch(): Rates;
}