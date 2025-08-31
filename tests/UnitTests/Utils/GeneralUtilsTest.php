<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\GeneralUtils;

/**
 * @phpstan-type DebugTrace array<array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: array<mixed>, object?: object}>
 */
class TestOnlyGeneralUtils extends GeneralUtils {
    public function testOnlyGetRandomIvForAlgo(string $algo): string {
        return $this->getRandomIvForAlgo($algo);
    }

    /** @return DebugTrace */
    public function testOnlyGetTrace(): array {
        return $this->testOnlyGetTrace1();
    }

    /** @return DebugTrace */
    protected function testOnlyGetTrace1(): array {
        return $this->testOnlyGetTrace2();
    }

    /** @return DebugTrace */
    protected function testOnlyGetTrace2(): array {
        return debug_backtrace();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\GeneralUtils
 */
final class GeneralUtilsTest extends UnitTestCase {
    public function testFromEnv(): void {
        $this->assertEquals(new GeneralUtils(), GeneralUtils::fromEnv());
    }

    public function testCheckNotNull(): void {
        $general_utils = new GeneralUtils();

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotNull(false, 'should never be null');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotNull(0, 'should never be null');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotNull('', 'should never be null');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotNull(true, 'should never be null');
        try {
            // @phpstan-ignore-next-line method.alreadyNarrowedType
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
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotFalse(true, 'should never be false');
        try {
            // @phpstan-ignore-next-line method.alreadyNarrowedType
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

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotBool(null, 'should never be bool');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotBool(0, 'should never be bool');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotBool('', 'should never be bool');
        try {
            // @phpstan-ignore-next-line method.alreadyNarrowedType
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
            // @phpstan-ignore-next-line method.alreadyNarrowedType
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

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotEmpty(null, 'should never be empty');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotEmpty(false, 'should never be empty');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotEmpty(0, 'should never be empty');
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $general_utils->checkNotEmpty(true, 'should never be empty');
        try {
            // @phpstan-ignore-next-line method.alreadyNarrowedType
            $general_utils->checkNotEmpty('', 'should never be empty');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** should never be empty",
            ], $this->getLogs());
            $this->assertSame('GeneralUtilsTest.php:*** should never be empty', $exc->getMessage());
        }
    }

    public function testCheckComputedMessage(): void {
        $general_utils = new GeneralUtils();
        try {
            // @phpstan-ignore-next-line method.alreadyNarrowedType
            $general_utils->checkNotNull(null, fn () => md5('somehow computed'));
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "ERROR GeneralUtilsTest.php:*** bb12553cd7e12de8b38f89c03787e6e4",
            ], $this->getLogs());
            $this->assertSame(
                'GeneralUtilsTest.php:*** bb12553cd7e12de8b38f89c03787e6e4',
                $exc->getMessage(),
            );
        }
    }

    public function testEscape(): void {
        $general_utils = new GeneralUtils();
        $this->assertSame('abc', $general_utils->escape('abc', []));
        $this->assertSame('\abc', $general_utils->escape('abc', ['a']));
        $this->assertSame('\a\b\c', $general_utils->escape('abc', ['a', 'b', 'c']));

        // \\a and \\\\a are nonsensical with '
        $this->assertSame('\a \\\a \\\\\a', $general_utils->escape('a \a \\\a', ['a']));
        $this->assertSame('a \\\a \\\\\a', $general_utils->escape('a \a \\\a', ['\a']));
        $this->assertSame('a \a \\\\\a', $general_utils->escape('a \a \\\a', ['\\\a']));

        // \a and \\\a are nonsensical with "
        $this->assertSame("\\a \\\\a \\\\\\a", $general_utils->escape("a \\a \\\\a", ["a"]));
        $this->assertSame("a \\\\a \\\\\\a", $general_utils->escape("a \\a \\\\a", ["\\a"]));
        $this->assertSame("a \\a \\\\\\a", $general_utils->escape("a \\a \\\\a", ["\\\\a"]));

        // \\t and \\\\t are nonsensical with '
        $this->assertSame('\t \\\t \\\\\t', $general_utils->escape('t \t \\\t', ['t']));
        $this->assertSame('t \\\t \\\\\t', $general_utils->escape('t \t \\\t', ['\t']));
        $this->assertSame('t \t \\\\\t', $general_utils->escape('t \t \\\t', ['\\\t']));

        // everything makes sense with "
        $this->assertSame("\\t \t \\\\t \\\t", $general_utils->escape("t \t \\t \\\t", ["t"]));
        $this->assertSame("t \\\t \\t \\\\\t", $general_utils->escape("t \t \\t \\\t", ["\t"]));
        $this->assertSame("t \t \\\\t \\\t", $general_utils->escape("t \t \\t \\\t", ["\\t"]));
        $this->assertSame("t \t \\t \\\\\t", $general_utils->escape("t \t \\t \\\t", ["\\\t"]));
    }

    public function testUnescape(): void {
        $general_utils = new GeneralUtils();
        $this->assertSame('\a\b\c', $general_utils->unescape('\a\b\c', []));
        $this->assertSame('a\b\c', $general_utils->unescape('\a\b\c', ['a']));
        $this->assertSame('abc', $general_utils->unescape('\a\b\c', ['a', 'b', 'c']));

        // \\a and \\\\a are nonsensical with '
        $this->assertSame('a a \a \\\a', $general_utils->unescape('a \a \\\a \\\\\a', ['a']));
        $this->assertSame('a \a \a \\\a', $general_utils->unescape('a \a \\\a \\\\\a', ['\a']));
        $this->assertSame('a \a \\\a \\\a', $general_utils->unescape('a \a \\\a \\\\\a', ['\\\a']));

        // \a and \\\a are nonsensical with "
        $this->assertSame("a a \\a \\\\a", $general_utils->unescape("a \\a \\\\a \\\\\\a", ["a"]));
        $this->assertSame("a \\a \\a \\\\a", $general_utils->unescape("a \\a \\\\a \\\\\\a", ["\\a"]));
        $this->assertSame("a \\a \\\\a \\\\a", $general_utils->unescape("a \\a \\\\a \\\\\\a", ["\\\\a"]));

        // \\t and \\\\t are nonsensical with '
        $this->assertSame('t t \t \\\t', $general_utils->unescape('t \t \\\t \\\\\t', ['t']));
        $this->assertSame('t \t \t \\\t', $general_utils->unescape('t \t \\\t \\\\\t', ['\t']));
        $this->assertSame('t \t \\\t \\\t', $general_utils->unescape('t \t \\\t \\\\\t', ['\\\t']));

        // everything makes sense with "
        $this->assertSame("t \t t \\\t \\t \\\\\t", $general_utils->unescape("t \t \\t \\\t \\\\t \\\\\t", ["t"]));
        $this->assertSame("t \t \\t \t \\\\t \\\t", $general_utils->unescape("t \t \\t \\\t \\\\t \\\\\t", ["\t"]));
        $this->assertSame("t \t \\t \\\t \\t \\\\\t", $general_utils->unescape("t \t \\t \\\t \\\\t \\\\\t", ["\\t"]));
        $this->assertSame("t \t \\t \\\t \\\\t \\\t", $general_utils->unescape("t \t \\t \\\t \\\\t \\\\\t", ["\\\t"]));
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

    public function testEncryptUnserializeableData(): void {
        $general_utils = new GeneralUtils();
        $key = 'asdf';

        try {
            $general_utils->encrypt($key, NAN);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('encrypt: json_encode failed', $exc->getMessage());
        }
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

    public function testHash(): void {
        $general_utils = new GeneralUtils();
        $key = 'asdf';

        $result = $general_utils->hash($key, 'test');

        $this->assertSame('gzil1tQpxPZhji4C4pbig1cPRjeLx-Ko27s5lZnPYJY', $result);
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
        $pretty_trace = $general_utils->getPrettyTrace($trace);
        $this->assertStringStartsWith('Stack trace:', $pretty_trace);
        $this->assertMatchesRegularExpression('/\#0 \S+\.php\:[0-9]+ \- testGetPrettyTrace\(\)/', $pretty_trace);
        $this->assertMatchesRegularExpression('/phpunit/', $pretty_trace);
    }

    public function testGetPrettyTraceComplex(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $trace = $general_utils->testOnlyGetTrace();
        $pretty_trace = $general_utils->getPrettyTrace($trace);
        $this->assertStringStartsWith('Stack trace:', $pretty_trace);
        $this->assertMatchesRegularExpression('/\#0 \S+tests\/UnitTests\/Utils\/GeneralUtilsTest\.php:[0-9]+ \- testOnlyGetTrace2\(\)/', $pretty_trace);
        $this->assertMatchesRegularExpression('/\#1 \S+tests\/UnitTests\/Utils\/GeneralUtilsTest\.php:[0-9]+ \- testOnlyGetTrace1\(\)/', $pretty_trace);
        $this->assertMatchesRegularExpression('/\#2 \S+tests\/UnitTests\/Utils\/GeneralUtilsTest\.php:[0-9]+ \- testOnlyGetTrace\(\)/', $pretty_trace);
        $this->assertMatchesRegularExpression('/\#3 \S+\.php\:[0-9]+ \- testGetPrettyTraceComplex\(\)/', $pretty_trace);
        $this->assertMatchesRegularExpression('/phpunit/', $pretty_trace);
    }

    public function testGetTraceOverview(): void {
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $this->assertSame('', $general_utils->getTraceOverview($trace));
    }

    public function testGetTraceOverviewComplex(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $trace = $general_utils->testOnlyGetTrace();
        $this->assertSame(
            'GeneralUtilsTest>TestOnlyGeneralUtils',
            $general_utils->getTraceOverview($trace),
        );
    }

    public function testMeasureLatency(): void {
        $general_utils = new GeneralUtils();
        [$result, $msg] = $general_utils->measureLatency(function () {
            return 'test';
        });
        $this->assertSame('test', $result);
        $this->assertMatchesRegularExpression('/took [0-9\.]+ms/', $msg);
    }

    public function testRemoveRecursive(): void {
        $data_path = $this->envUtils()->getDataPath();
        mkdir("{$data_path}/parent");
        file_put_contents("{$data_path}/parent/file.txt", '');
        mkdir("{$data_path}/parent/child");
        file_put_contents("{$data_path}/parent/child/file.txt", '');

        $this->assertTrue(is_dir("{$data_path}/parent"));
        $this->assertTrue(is_file("{$data_path}/parent/file.txt"));
        $this->assertTrue(is_dir("{$data_path}/parent/child"));
        $this->assertTrue(is_file("{$data_path}/parent/child/file.txt"));

        $general_utils = new GeneralUtils();
        $general_utils->removeRecursive("{$data_path}/parent");

        $this->assertFalse(is_dir("{$data_path}/parent"));
        $this->assertFalse(is_file("{$data_path}/parent/file.txt"));
        $this->assertFalse(is_dir("{$data_path}/parent/child"));
        $this->assertFalse(is_file("{$data_path}/parent/child/file.txt"));
    }

    public function testGetRandomIvForAlgo(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9+\/]{16}$/',
            base64_encode($general_utils->testOnlyGetRandomIvForAlgo('aes-256-gcm'))
        );
    }

    public function testGetRandomIvForAlgoZeroLength(): void {
        $general_utils = new TestOnlyGeneralUtils();
        $this->assertSame(
            '',
            base64_encode($general_utils->testOnlyGetRandomIvForAlgo('aes-128-ecb'))
        );
    }

    public function testGetRandomIvForUnknownAlgo(): void {
        $general_utils = new TestOnlyGeneralUtils();
        try {
            $general_utils->testOnlyGetRandomIvForAlgo('unknown');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertMatchesRegularExpression('/Unknown cipher algorithm/', $exc->getMessage());
        }
    }
}
