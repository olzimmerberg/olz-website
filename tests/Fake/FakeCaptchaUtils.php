<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Captcha\Utils\CaptchaUtils;

class FakeCaptchaUtils extends CaptchaUtils {
    public function getRandomString(int $length): string {
        return base64_encode(str_repeat('a', $length));
    }

    public function validateToken(?string $token): bool {
        if (preg_match('/invalid/', $token ?? '')) {
            return false;
        }
        return true;
    }
}
