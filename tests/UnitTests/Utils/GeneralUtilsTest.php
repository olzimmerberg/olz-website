<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

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
        $general_utils = new GeneralUtils();
        $key = 'asdf';

        $token = $general_utils->encrypt($key, ['test' => 'data']);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\\-_]+$/', $token);
        $this->assertSame(
            ['test' => 'data'],
            $general_utils->decrypt($key, $token)
        );
    }

    public function testDecryptInvalidToken(): void {
        $general_utils = new GeneralUtils();
        $key = 'asdf';

        $this->assertSame(null, $general_utils->decrypt($key, ''));
    }

    public function testBinarySearchOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [1, 2, 4];
        $this->assertSame(2, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            }, 0, 3,
        ));
    }

    public function testBinarySearchAfterAllOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [1, 2, 4];
        $this->assertSame(3, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 5 <=> $list[$index];
            }, 0, 3,
        ));
    }

    public function testBinarySearchBeforeAllOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 3, 5];
        $this->assertSame(0, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 1 <=> $list[$index];
            }, 0, 3,
        ));
    }

    public function testBinarySearchEven(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 4];
        $this->assertSame(1, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            }, 0, 2,
        ));
    }

    public function testBinarySearchAfterAllEven(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 4];
        $this->assertSame(2, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 5 <=> $list[$index];
            }, 0, 2,
        ));
    }

    public function testBinarySearchBeforeAllEven(): void {
        $general_utils = new GeneralUtils();
        $list = [3, 5];
        $this->assertSame(0, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 2 <=> $list[$index];
            }, 0, 2,
        ));
    }

    public function testBinarySearchEmptyList(): void {
        $general_utils = new GeneralUtils();
        $list = [];
        $this->assertSame(0, $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            }, 0, 0,
        ));
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
