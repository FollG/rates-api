<?php

declare(strict_types=1);

namespace app\exceptions;

final class ValidationException extends ApiException
{
    public function __construct(
        string $message,
    ) {
        parent::__construct(
            $message,
        );
    }
}