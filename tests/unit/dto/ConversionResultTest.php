<?php

declare(strict_types=1);

namespace app\tests\unit\dto;

use PHPUnit\Framework\TestCase;
use app\dto\ConversionResult;
use DateTimeImmutable;


final class ConversionResultTest extends TestCase
{
    public function testCreatesResult(): void
    {
        $date = new DateTimeImmutable();

        $result =
            new ConversionResult(
                'BTC',
                'ETH',
                '1',
                '20',
                '20',
                $date
            );

        self::assertEquals(
            'BTC',
            $result->currencyFrom
        );

        self::assertEquals(
            $date,
            $result->fetchedAt
        );
    }
}