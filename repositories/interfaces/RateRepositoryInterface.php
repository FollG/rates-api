<?php

declare(strict_types=1);

namespace app\repositories\interfaces;

use app\dto\Rates;

interface RateRepositoryInterface
{
    public function getRates(): Rates;
}