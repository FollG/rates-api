<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\BearerAuthBehavior;
use app\components\RateLimitBehavior;
use app\components\RedisCache;
use app\dto\ApiResponse;
use app\services\ConversionService;
use app\services\dispatchers\ApiDispatcher;
use app\services\RatesService;
use app\requests\ConvertRequest;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

final class ApiController extends Controller
{
    const SUCCESS = "success";
    public $enableCsrfValidation = false;

    public function __construct(
        $id,
        $module,
        private readonly ApiDispatcher $dispatcher,
        $config = [],
    ) {
        parent::__construct(
            $id,
            $module,
            $config
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function behaviors(): array
    {
        return [
            [
                'class'=>BearerAuthBehavior::class
            ],
            'rateLimit' => [
                'class' => RateLimitBehavior::class,
                'cache' => Yii::createObject(
                    RedisCache::class
                ),
                'limit' => Yii::$app->params['rateLimit'],
                'window' => Yii::$app->params['rateLimitWindow'],
            ],
        ];
    }

    /**
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionIndex(): ApiResponse
    {
        return $this->dispatcher->dispatch(
            $this->request->getQueryParam('method'),
            $this->request->getMethod(),
            array_merge(
                $this->request->getQueryParams(),
                $this->request->post()
            )
        );
    }
}