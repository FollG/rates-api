<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\BearerAuthBehavior;
use app\components\RateLimitBehavior;
use app\components\RedisCache;
use app\services\ConversionService;
use app\services\RatesService;
use app\requests\ConvertRequest;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

final class ApiController extends Controller
{
    const SUCCESS = "success";
    public $enableCsrfValidation = false;

    public function __construct(
        $id,
        $module,
        private readonly RatesService $ratesService,
        private readonly ConversionService $conversionService,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
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

    public function actionIndex(): Response|array
    {
        $method = $this->request->getMethod();
        $params = $this->request->getQueryParams();

        switch ($params['method'] ?? null) {
            case 'rates':
                if ($method !== 'GET') {
                    exit(1);
                }

                return $this->rates();

            case 'convert':
                if ($method !== 'POST') {
                    exit(2);
                }

                return $this->convert($params);

            default:
                exit(3);
        }
    }

    private function methodNotAllowed(): Response
    {
        return new Response([
            'status' => 'error',
            'code' => 405,
            'message' => 'Method Not Allowed',
        ]);
    }

    private function notFound(): Response
    {
        return new Response([
            'status' => 'error',
            'code' => 404,
            'message' => 'Not found',
        ]);
    }

    private function rates(): array
    {
        return [
            'status' => self::SUCCESS,
            'code' => 200,
            'fetched_at' => (new DateTime())->format(DATE_ATOM),
            'data' => $this->serializeRates(),
        ];
    }

    /**
     */
    private function convert(array $data): array
    {
        $request = new ConvertRequest(
            currencyFrom: $data['currency_from'],
            currencyTo: $data['currency_to'],
            value: $data['value'],
        );

        $result = $this->conversionService->convert($request);

        return [
            'status' => self::SUCCESS,
            'code' => 200,
            'fetched_at' => $result->fetchedAt->format(DATE_ATOM),
            'data' => [
                'currency_from' => $result->currencyFrom,
                'currency_to' => $result->currencyTo,
                'value' => $result->value,
                'rate' => $result->rate,
                'converted_value' => $result->convertedValue,
            ],
        ];
    }

    private function serializeRates(): array
    {
        $rates = $this->ratesService->getRatesWithCommission();

        return $rates->toArray();
    }
}