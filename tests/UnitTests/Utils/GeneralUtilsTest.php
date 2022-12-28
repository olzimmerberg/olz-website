<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;

class TestOnlyGeneralUtils extends GeneralUtils {
    public function testOnlyGetRandomIvForAlgo($algo) {
        return $this->getRandomIvForAlgo($algo);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\GeneralUtils
 */
final class GeneralUtilsTest extends UnitTestCase {
    public function testBase64EncodeUrl(): void {
        $general_utils = new GeneralUtils();
        $binary_string = base64_decode("+/A="); // contains all special chars
        $this->assertSame('-_A', $general_utils->base64EncodeUrl($binary_string));
    }

    public function testBase64DecodeUrl(): void {
        $general_utils = new GeneralUtils();
        $binary_string = base64_decode("+/A="); // contains all special chars
        $this->assertSame($binary_string, $general_utils->base64DecodeUrl('-_A'));
    }

    public function testEncryptDecryptData(): void {
        $logger = Fake\FakeLogger::create();
        $general_utils = new GeneralUtils();
        $general_utils->setLog($logger);
        $key = 'asdf';

        $token = $general_utils->encrypt($key, ['test' => 'data']);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\\-_]+$/', $token);
        $this->assertSame(
            ['test' => 'data'],
            $general_utils->decrypt($key, $token)
        );
    }

    public function testDecryptInvalidToken(): void {
        $logger = Fake\FakeLogger::create();
        $general_utils = new GeneralUtils();
        $general_utils->setLog($logger);
        $key = 'asdf';

        $this->assertSame(null, $general_utils->decrypt($key, ''));
    }

    public function testGetPrettyTrace(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertMatchesRegularExpression('/phpunit/', $general_utils->getPrettyTrace($trace));
    }

    public function testGetRandomIvForAlgo(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9+\/]{16}$/',
            base64_encode($general_utils->testOnlyGetRandomIvForAlgo('aes-256-gcm'))
        );
    }
}
