<?php

declare(strict_types=1);

namespace app\tests\unit\mappers;

use PHPUnit\Framework\TestCase;
use app\mappers\ConversionResponseMapper;
use app\dto\ConversionResult;
use DateTimeImmutable;


final class ConversionResponseMapperTest extends TestCase
{
    public function testMapsConversion(): void
    {

        $result =
            new ConversionResult(
                'BTC',
                'ETH',
                '1',
                '20',
                '20',
                new DateTimeImmutable()
            );

        $mapper = new ConversionResponseMapper();
        $response = $mapper->map($result);

        self::assertEquals(
            'BTC',
            $response->data['currency_from']
        );

        self::assertEquals(
            'ETH',
            $response->data['currency_to']
        );

        self::assertEquals(
            '20',
            $response->data['converted_value']
        );
    }
}