<?php

namespace Olz\Exceptions;

class InvalidCredentialsException extends \Exception {
    public function __construct(
        string $message = "",
        protected int $num_remaining_attempts = 0,
        int $code = 0,
        $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getNumRemainingAttempts() {
        return $this->num_remaining_attempts;
    }
}
