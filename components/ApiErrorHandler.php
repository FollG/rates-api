<?php

declare(strict_types=1);

namespace app\components;

use app\exceptions\ApiException;
use Yii;
use yii\web\ErrorHandler;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\Response;
use Throwable;


final class ApiErrorHandler extends ErrorHandler
{
    public function renderException($exception): void
    {
        $response = Yii::$app->response;

        $response->format = Response::FORMAT_JSON;

        $code = $this->getStatusCode($exception);

        $response->statusCode = $code;

        $response->data = [
            'status' => 'error',
            'code' => $code,
            'message' => $this->getMessage($exception),
        ];

        $response->send();
    }


    private function getStatusCode(
        Throwable $exception
    ): int {
        if ($exception instanceof ApiException) {
            return $exception->statusCode();
        }

        if ($exception instanceof HttpException) {
            return $exception->statusCode;
        }

        return 500;
    }

    private function getMessage(
        Throwable $exception
    ): string {

        if (YII_DEBUG) {
            return $exception->getMessage();
        }


        return match (true) {

            $exception instanceof ForbiddenHttpException =>
            'Forbidden',

            default =>
            'Internal server error',
        };
    }
}