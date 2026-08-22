<?php

declare(strict_types=1);

namespace app\services\dispatchers;

use app\dto\ApiResponse;
use app\mappers\ConversionResponseMapper;
use app\mappers\RatesResponseMapper;
use app\requests\ConvertRequest;
use app\services\ConversionService;
use app\services\RatesService;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;

final readonly class ApiDispatcher
{
    public function __construct(
        private RatesService $ratesService,
        private ConversionService $conversionService,
        private RatesResponseMapper $ratesMapper,
        private ConversionResponseMapper $conversionMapper,
    ) {
    }

    /**
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function dispatch(
        ?string $method,
        string $httpMethod,
        array $params
    ): ApiResponse
    {
        return match ($method) {
            'rates' => $this->rates($httpMethod, $params['currency'] ?? null),
            'convert' => $this->convert($httpMethod, $params),

            default => throw new BadRequestHttpException('Unknown method'),
        };
    }

    /**
     * @throws MethodNotAllowedHttpException
     */
    private function rates(string $httpMethod, ?string $currencies): ApiResponse
    {

        if ($httpMethod !== 'GET') {
            throw new MethodNotAllowedHttpException();
        }

        return $this->ratesMapper->map(
            $this->ratesService->getRatesWithCommission($currencies),
        );
    }

    /**
     * @throws MethodNotAllowedHttpException
     */
    private function convert(
        string $httpMethod,
        array $params
    ): ApiResponse
    {

        if ($httpMethod !== 'POST') {
            throw new MethodNotAllowedHttpException();
        }

        $request = ConvertRequest::fromArray($params);
        $result = $this->conversionService->convert($request);

        return $this->conversionMapper->map(
            $result
        );
    }
}