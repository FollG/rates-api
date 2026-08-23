<?php

declare(strict_types=1);

namespace app\domain\enum;


enum CurrencyEnum: string
{
    case FIAT = 'fiat';

    case CRYPTO = 'crypto';
}