<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\RecaptchaUtils;

class FakeRecaptchaUtils extends RecaptchaUtils {
    public function validateRecaptchaToken(string $token): bool {
        if (preg_match('/invalid/', $token)) {
            return false;
        }
        return true;
    }
}
