<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\TooManyRequestsHttpException;

final class RateLimitBehavior extends Behavior
{
    public RedisCache $cache;

    public int $limit = 60;

    public int $window = 60;


    public function events(): array
    {
        return [
            \yii\base\Controller::EVENT_BEFORE_ACTION => 'check',
        ];
    }


    public function check(): void
    {
        $token = $this->getToken();


        if ($token === null) {
            return;
        }


        $bucket = intdiv(
            time(),
            $this->window
        );


        $key = sprintf(
            'rate_limit:%s:%s',
            sha1($token),
            $bucket
        );


        $current = $this->cache->get($key);


        $count = $current === null
            ? 1
            : ((int)$current + 1);



        if ($count > $this->limit) {

            throw new TooManyRequestsHttpException(
                'Rate limit exceeded'
            );

        }



        $this->cache->set(
            $key,
            (string)$count,
            $this->window
        );
    }



    private function getToken(): ?string
    {
        $header =
            Yii::$app
                ->request
                ->headers
                ->get('Authorization');


        if (!$header) {
            return null;
        }


        if (!str_starts_with(
            $header,
            'Bearer '
        )) {
            return null;
        }


        return trim(
            substr($header, 7)
        );
    }
}