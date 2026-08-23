<?php

declare(strict_types=1);

namespace app\tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\CommissionService;


final class CommissionServiceTest extends TestCase
{

    public function testApplyCommission(): void
    {
        $service = new CommissionService('0.98');
        $result = $service->apply(100);

        self::assertEquals(
            98,
            $result
        );
    }


    public function testZeroCommission(): void
    {
        $service = new CommissionService('1');

        self::assertEquals(
            100,
            $service->apply(100)
        );
    }
}