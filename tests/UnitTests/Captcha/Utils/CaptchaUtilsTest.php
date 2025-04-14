<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Captcha\Utils;

use Olz\Captcha\Utils\CaptchaUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class TestOnlyCaptchaUtils extends CaptchaUtils {
    public function getAppEnv(): string {
        return 'dev';
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
            'log' => ['D32,170', 'M66,170', 'U100,170'],
            'config' => [
                'rand' => 'TOQj',
                'date' => '2020-03-13 19:30:00',
                'mac' => '7VkwKEKhbXMzike6tOZE928V-9_mBKjQRNAW6smiJDw',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertTrue($utils->validateToken($token));
    }

    public function testValidateDevToken(): void {
        $utils = new TestOnlyCaptchaUtils();
        $this->assertTrue($utils->validateToken('dev'));
    }

    public function testValidateNullToken(): void {
        $utils = new CaptchaUtils();
        $this->assertFalse($utils->validateToken(null));
    }

    public function testValidateEmptyToken(): void {
        $utils = new CaptchaUtils();
        $this->assertFalse($utils->validateToken(''));
    }

    public function testValidateTokenInvalidMac(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['D1,2', 'M1,2', 'U1,2'],
            'config' => [
                'rand' => 'TOQj',
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

    public function testValidateTokenTooShort(): void {
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
        $this->assertFalse($utils->validateToken($token));
    }

    public function testValidateTokenMalformedEntry(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['invalid'],
            'config' => [
                'rand' => 'TOQj',
                'date' => '2020-03-13 19:30:00',
                'mac' => '7VkwKEKhbXMzike6tOZE928V-9_mBKjQRNAW6smiJDw',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertFalse($utils->validateToken($token));
    }

    public function testValidateTokenConstraintViolated(): void {
        $utils = new CaptchaUtils();
        $token_content = [
            'log' => ['D1,2', 'M1,2', 'U1,2'], // not a plausible way to solve the captcha
            'config' => [
                'rand' => 'TOQj',
                'date' => '2020-03-13 19:30:00',
                'mac' => '7VkwKEKhbXMzike6tOZE928V-9_mBKjQRNAW6smiJDw',
            ],
        ];
        $token = base64_encode(json_encode($token_content) ?: '');
        $this->assertFalse($utils->validateToken($token));
    }
}
