<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;

class TestOnlyGeneralUtils extends GeneralUtils {
    public function testOnlyGetRandomIvForAlgo(string $algo): string {
        return $this->getRandomIvForAlgo($algo);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\GeneralUtils
 */
final class GeneralUtilsTest extends UnitTestCase {
    public function testCheckNotNull(): void {
        $general_utils = new GeneralUtils();

        $general_utils->checkNotNull(false, 'should never be null');
        $general_utils->checkNotNull(0, 'should never be null');
        $general_utils->checkNotNull('', 'should never be null');
        $general_utils->checkNotNull(true, 'should never be null');
        try {
            $general_utils->checkNotNull(null, 'should never be null');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be null",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be null', $exc->getMessage());
        }
    }

    public function testCheckNotFalse(): void {
        $general_utils = new GeneralUtils();

        $general_utils->checkNotFalse(null, 'should never be false');
        $general_utils->checkNotFalse(0, 'should never be false');
        $general_utils->checkNotFalse('', 'should never be false');
        $general_utils->checkNotFalse(true, 'should never be false');
        try {
            $general_utils->checkNotFalse(false, 'should never be false');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be false",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be false', $exc->getMessage());
        }
    }

    public function testCheckNotBool(): void {
        $general_utils = new GeneralUtils();

        $general_utils->checkNotBool(null, 'should never be bool');
        $general_utils->checkNotBool(0, 'should never be bool');
        $general_utils->checkNotBool('', 'should never be bool');
        try {
            $general_utils->checkNotBool(false, 'should never be bool');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be bool",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be bool', $exc->getMessage());
        }
        $this->resetLogs();
        try {
            $general_utils->checkNotBool(true, 'should never be bool');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be bool",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be bool', $exc->getMessage());
        }
    }

    public function testCheckNotEmpty(): void {
        $general_utils = new GeneralUtils();

        $general_utils->checkNotEmpty(null, 'should never be empty');
        $general_utils->checkNotEmpty(false, 'should never be empty');
        $general_utils->checkNotEmpty(0, 'should never be empty');
        $general_utils->checkNotEmpty(true, 'should never be empty');
        try {
            $general_utils->checkNotEmpty('', 'should never be empty');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be empty",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be empty', $exc->getMessage());
        }
    }

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

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\-_]+$/', $token);
        $this->assertSame(
            ['test' => 'data'],
            $general_utils->decrypt($key, $token)
        );
    }

    public function testDecryptInvalidToken(): void {
        $general_utils = new GeneralUtils();
        $key = 'asdf';

        try {
            $general_utils->decrypt($key, '');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('decrypt: json_decode failed', $exc->getMessage());
        }
    }

    public function testBinarySearchBeforeAllOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 3, 5];
        $this->assertSame([0, -1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 1 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchExactFirstOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 3, 5];
        $this->assertSame([0, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 2 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchExactMiddleOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 3, 5];
        $this->assertSame([1, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [1, 2, 4];
        $this->assertSame([2, -1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchExactLastOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [1, 2, 4];
        $this->assertSame([2, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 4 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchAfterAllOdd(): void {
        $general_utils = new GeneralUtils();
        $list = [1, 2, 4];
        $this->assertSame([2, 1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 5 <=> $list[$index];
            },
            0,
            3,
        ));
    }

    public function testBinarySearchBeforeAllEven(): void {
        $general_utils = new GeneralUtils();
        $list = [3, 5];
        $this->assertSame([0, -1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 2 <=> $list[$index];
            },
            0,
            2,
        ));
    }

    public function testBinarySearchExactFirstEven(): void {
        $general_utils = new GeneralUtils();
        $list = [3, 5];
        $this->assertSame([0, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            },
            0,
            2,
        ));
    }

    public function testBinarySearchEven(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 4];
        $this->assertSame([1, -1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 3 <=> $list[$index];
            },
            0,
            2,
        ));
    }

    public function testBinarySearchExactLastEven(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 4];
        $this->assertSame([1, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 4 <=> $list[$index];
            },
            0,
            2,
        ));
    }

    public function testBinarySearchAfterAllEven(): void {
        $general_utils = new GeneralUtils();
        $list = [2, 4];
        $this->assertSame([1, 1], $general_utils->binarySearch(
            function ($index) use ($list) {
                return 5 <=> $list[$index];
            },
            0,
            2,
        ));
    }

    public function testBinarySearchEmptyList(): void {
        $general_utils = new GeneralUtils();
        $list = [];
        $this->assertSame([0, 0], $general_utils->binarySearch(
            function ($index) use ($list) {
                // @phpstan-ignore-next-line
                return 3 <=> $list[$index];
            },
            0,
            0,
        ));
    }

    public function testGetPrettyTrace(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertMatchesRegularExpression('/phpunit/', $general_utils->getPrettyTrace($trace));
    }

    public function testGetTraceOverview(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertSame('', $general_utils->getTraceOverview($trace));
    }

    public function testMeasureLatency(): void {
        $general_utils = new GeneralUtils();
        [$result, $msg] = $general_utils->measureLatency(function () {
            return 'test';
        });
        $this->assertSame('test', $result);
        $this->assertMatchesRegularExpression('/took [0-9\.]+ms/', $msg);
    }

    public function testGetRandomIvForAlgo(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9+\/]{16}$/',
            base64_encode($general_utils->testOnlyGetRandomIvForAlgo('aes-256-gcm'))
        );
    }
}
