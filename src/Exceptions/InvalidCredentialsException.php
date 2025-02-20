<?php

namespace Olz\Exceptions;

class InvalidCredentialsException extends \Exception {
    /** @param int<0, max> $num_remaining_attempts */
    public function __construct(
        string $message = "",
        protected int $num_remaining_attempts = 0,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /** @return int<0, max> */
    public function getNumRemainingAttempts(): int {
        return $this->num_remaining_attempts;
    }
}
