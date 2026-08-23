<?php

declare(strict_types=1);

namespace app\dto;

use JsonSerializable;

readonly class ApiResponse implements JsonSerializable
{
    public function __construct(
        private string  $status,
        private int     $code,
        public mixed    $data = null,
        private ?string $message = null,
        private ?string $fetchedAt = null,
    ) {
    }

    public static function success(
        mixed $data,
        int $code = 200,
        ?string $fetchedAt = null,
    ): self {
        return new self(
            status: 'success',
            code: $code,
            fetchedAt: $fetchedAt,
            data: $data,
        );
    }

    public static function error(
        string $message,
        int $code,
    ): self {
        return new self(
            status: 'error',
            code: $code,
            message: $message,
        );
    }

    public function jsonSerialize(): array
    {
        $result = [
            'status' => $this->status,
            'code' => $this->code,
        ];

        if ($this->fetchedAt !== null) {
            $result['fetched_at'] = $this->fetchedAt;
        }

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        if ($this->message !== null) {
            $result['message'] = $this->message;
        }

        return $result;
    }
}