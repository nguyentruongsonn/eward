<?php

namespace App\Exceptions;

use RuntimeException;

class PasswordChangeException extends RuntimeException
{
    public function __construct(
        public readonly string $field,
        string $message,
        ?\Throwable $previous = null,
        public readonly string $errorCode = 'PASSWORD_CHANGE_ERROR',
        public readonly int $status = 422,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
