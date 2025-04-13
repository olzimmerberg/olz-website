<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Captcha\Utils;

use Olz\Captcha\Utils\CaptchaUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class TestOnlyCaptchaUtils extends CaptchaUtils {
    public function getRandomString(int $length): string {
        return base64_encode(str_repeat('a', $length));
    }
}

/**
 * @internal
 *
 * @covers \Olz\Captcha\Utils\CaptchaUtils
 */
final class CaptchaUtilsTest extends UnitTestCase {
    public function testGenerateOlzCaptchaConfig(): void {
        $utils = new TestOnlyCaptchaUtils();
        $this->assertSame([
            'rand' => 'YWE=',
            'date' => '2020-03-13 19:30:00',
            'mac' => 'uVL8vw53PKjj0OqjiBk6yf-4VAtACOdgRrwEC-tSsMQ',
        ], $utils->generateOlzCaptchaConfig(2));
    }

    public function testRandomGenerateOlzCaptchaConfig(): void {
        $utils = new CaptchaUtils();
        $result = $utils->generateOlzCaptchaConfig(100);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\/\+\=]{136}$/', $result['rand']);
        $this->assertSame('2020-03-13 19:30:00', $result['date']);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\_\-]{43}$/', $result['mac']);
    }

    public function testValidateValidToken(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['D1,2', 'M1,2', 'U1,2'],
            'config' => [
                'rand' => 'YWE=',
                'date' => '2020-03-13 19:30:00',
                'mac' => 'uVL8vw53PKjj0OqjiBk6yf-4VAtACOdgRrwEC-tSsMQ',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertTrue($utils->validateToken($token));
    }

    public function testValidateTokenInvalidMac(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['D1,2', 'M1,2', 'U1,2'],
            'config' => [
                'rand' => 'YWE=',
                'date' => '2020-03-13 19:30:00',
                'mac' => 'invalid',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertFalse($utils->validateToken($token));
    }

    public function testValidateTokenExpired(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['D1,2', 'M1,2', 'U1,2'],
            'config' => [
                'rand' => 'YWE=',
                'date' => '2020-03-13 19:25:00',
                'mac' => 'PG4SW6RfC-DoXkU-fzGMmPpUihJkBCduGbsxL-5izg8',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertFalse($utils->validateToken($token));
    }
}
