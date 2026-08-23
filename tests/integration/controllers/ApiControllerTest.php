<?php

declare(strict_types=1);

namespace app\tests\integration\controllers;

use PHPUnit\Framework\TestCase;
use Yii;
use app\controllers\ApiController;
use yii\base\InvalidConfigException;


final class ApiControllerTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function testControllerExists(): void
    {
        $controller = Yii::$app->createController('api');

        self::assertNotNull(
            $controller
        );

        self::assertInstanceOf(
            ApiController::class,
            $controller[0]
        );
    }
}