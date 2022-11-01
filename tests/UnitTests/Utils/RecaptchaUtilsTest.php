<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
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
 * @covers \Olz\Utils\RecaptchaUtils
 */
final class RecaptchaUtilsTest extends UnitTestCase {
    public function testValidateRecaptchaToken(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $logger = FakeLogger::create();
        $recaptcha_utils->setEnvUtils(new FakeEnvUtils());
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setLog($logger);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(true, $recaptcha_utils->validateRecaptchaToken('fake-recaptcha-token'));
        $this->assertSame([], $logger->handler->getPrettyRecords());
    }

    public function testValidateRecaptchaTokenInvalid(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $logger = FakeLogger::create();
        $recaptcha_utils->setEnvUtils(new FakeEnvUtils());
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setLog($logger);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(false, $recaptcha_utils->validateRecaptchaToken('invalid-recaptcha-token'));
        $this->assertSame([
            "NOTICE reCaptcha denied.",
        ], $logger->handler->getPrettyRecords());
    }

    public function testValidateRecaptchaTokenNull(): void {
        $recaptcha_utils = new RecaptchaUtils();
        $google_fetcher = new FakeRecaptchaUtilsGoogleFetcher();
        $logger = FakeLogger::create();
        $recaptcha_utils->setEnvUtils(new FakeEnvUtils());
        $recaptcha_utils->setGoogleFetcher($google_fetcher);
        $recaptcha_utils->setLog($logger);
        $recaptcha_utils->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $this->assertSame(false, $recaptcha_utils->validateRecaptchaToken('null-recaptcha-token'));
        $this->assertSame([
            "ERROR reCaptcha verification error.",
        ], $logger->handler->getPrettyRecords());
    }
}
