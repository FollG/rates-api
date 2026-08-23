<?php

declare(strict_types=1);

namespace app\tests\unit\dispatchers;

use PHPUnit\Framework\TestCase;
use app\services\dispatchers\ApiDispatcher;
use app\services\RatesService;
use app\services\ConversionService;
use app\mappers\RatesResponseMapper;
use app\mappers\ConversionResponseMapper;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;


final class ApiDispatcherTest extends TestCase
{
    private function dispatcher(): ApiDispatcher
    {
        return new ApiDispatcher(
            $this->createMock(RatesService::class),
            $this->createMock(ConversionService::class),
            $this->createMock(RatesResponseMapper::class),
            $this->createMock(ConversionResponseMapper::class)
        );
    }

    /**
     * @throws MethodNotAllowedHttpException
     */
    public function testUnknownMethodThrowsException(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $this->dispatcher()
            ->dispatch(
                'unknown',
                'GET',
                []
            );
    }

    /**
     * @throws BadRequestHttpException
     */
    public function testRatesAvailableOnlyByGet(): void
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $this->dispatcher()
            ->dispatch(
                'rates',
                'POST',
                []
            );
    }

    /**
     * @throws BadRequestHttpException
     */
    public function testConvertAvailableOnlyByPost(): void
    {
        $this->expectException(
            MethodNotAllowedHttpException::class
        );

        $this->dispatcher()
            ->dispatch(
                'convert',
                'GET',
                []
            );
    }
}