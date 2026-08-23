<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\web\ForbiddenHttpException;


final class BearerAuthBehavior extends Behavior
{

    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION
            => 'checkToken',
        ];
    }


    /**
     * @throws ForbiddenHttpException
     */
    public function checkToken(): void
    {
        $header =
            Yii::$app
                ->request
                ->headers
                ->get('Authorization');


        if (!$header) {
            throw new ForbiddenHttpException(
                'Invalid token'
            );
        }


        if (!str_starts_with(
            $header,
            'Bearer '
        )) {
            throw new ForbiddenHttpException(
                'Invalid token'
            );
        }


        $token = substr(
            $header,
            7
        );

        if (!hash_equals(
            Yii::$app->params['apiToken'],
            $token
        )) {

            throw new ForbiddenHttpException(
                'Invalid token'
            );
        }
    }
}