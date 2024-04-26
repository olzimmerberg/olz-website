<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\RecaptchaUtils;

class FakeRecaptchaUtilsGoogleFetcher {
    public function fetchRecaptchaVerification($siteverify_request_data) {
        $successful_request = [
            'secret' => 'some-secret-key',
            'response' => 'fake-recaptcha-token',
            'remoteip' => '1.2.3.4',
        ];
        if ($siteverify_request_data == $successful_request) {
            return ['success' => true];
        }
        $unsuccessful_request = [
            'secret' => 'some-secret-key',
            'response' => 'invalid-recaptcha-token',
            'remoteip' => '1.2.3.4',
        ];
        if ($siteverify_request_data == $unsuccessful_request) {
            return ['success' => false];
        }
        $null_request = [
            'secret' => 'some-secret-key',
            'response' => 'null-recaptcha-token',
            'remoteip' => '1.2.3.4',
        ];
        if ($siteverify_request_data == $null_request) {
            return null;
        }
        $request_json = json_encode($siteverify_request_data);
        throw new \Exception("Unexpected Request: {$request_json}");
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
class RecaptchaUtilsForTest extends RecaptchaUtils {
    public static function testOnlyResetCache() {
        parent::$cache = [];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\RecaptchaUtils
 */
final class RecaptchaUtilsTest extends UnitTestCase {
    public function testValidateRecaptchaTokenDevEnv(): void {
        $env_utils = new Fake\FakeEnvUtils();
        $env_utils->app_env = 'dev';
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $recaptcha_utils->setEnvUtils($env_utils);
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $recaptcha_utils->validateRecaptchaToken('fake-recaptcha-token');

        $this->assertSame([
            "NOTICE Accept recaptcha, because env is 'dev'",
        ], $this->getLogs());
        $this->assertTrue($result);
    }

    public function testValidateRecaptchaToken(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $recaptcha_utils->validateRecaptchaToken('fake-recaptcha-token');

        $this->assertSame([], $this->getLogs());
        $this->assertTrue($result);
    }

    public function testValidateRecaptchaTokenInvalid(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $recaptcha_utils->validateRecaptchaToken('invalid-recaptcha-token');

        $this->assertSame([
            "NOTICE reCaptcha denied.",
        ], $this->getLogs());
        $this->assertFalse($result);
    }

    public function testValidateRecaptchaTokenNull(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $recaptcha_utils->validateRecaptchaToken('null-recaptcha-token');

        $this->assertSame([
            "ERROR reCaptcha verification error.",
        ], $this->getLogs());
        $this->assertFalse($result);
    }

    public function testValidateRecaptchaTokenCached(): void {
        RecaptchaUtilsForTest::testOnlyResetCache();
        $recaptcha_utils = new RecaptchaUtilsForTest();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $recaptcha_utils->validateRecaptchaToken('fake-recaptcha-token');

        $result = $recaptcha_utils->validateRecaptchaToken('fake-recaptcha-token');

        $this->assertSame([
            "INFO Using cached recaptcha response...",
        ], $this->getLogs());
        $this->assertTrue($result);
    }
}
