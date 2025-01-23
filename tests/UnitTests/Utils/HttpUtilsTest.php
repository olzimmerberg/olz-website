<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\PhpStan\IsoDate;

/**
 * TODO: string -> int and string -> float.
 *
 * @phpstan-import-type TestAlias from ExitException
 *
 * @extends HttpParams<array{
 *   argInt: numeric-string,
 *   argMaybeFloat?: ?numeric-string,
 *   stringOrNull: ?non-empty-string,
 *   date: IsoDate,
 *   alias: TestAlias,
 * }>
 */
class TestParams extends HttpParams {
    public function configure(): void {
        $this->phpStanUtils->registerApiObject(IsoDate::class);
        $this->phpStanUtils->registerTypeImport(ExitException::class);
    }
}

/**
 * @phpstan-type TestAlias array<string>
 */
class ExitException extends \Exception {
}

/**
 * @internal
 *
 * @coversNothing
 */
class HttpUtilsForTest extends HttpUtils {
    public ?int $http_response_code = null;
    /** @var array<string> */
    public array $http_header_lines = [];
    public ?string $http_body = null;

    protected function sendHttpResponseCode(int $http_response_code): void {
        $this->http_response_code = $http_response_code;
    }

    protected function sendHeader(string $http_header_line): void {
        $this->http_header_lines[] = $http_header_line;
    }

    protected function sendHttpBody(string $http_body): void {
        $this->http_body = $http_body;
    }

    protected function exitExecution(): void {
        throw new ExitException();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsTest extends UnitTestCase {
    public function testValidateGetParamsMinimal(): void {
        $utils = new HttpUtilsForTest();

        $this->assertEquals([
            'argInt' => '3',
            'stringOrNull' => null,
            'date' => new IsoDate('2024-12-25'),
            'alias' => [],
        ], $utils->validateGetParams(TestParams::class, [
            'argInt' => '3',
            'stringOrNull' => null,
            'date' => '2024-12-25',
            'alias' => [],
        ]));
    }

    public function testValidateGetParamsMaximal(): void {
        $utils = new HttpUtilsForTest();

        $this->assertEquals([
            'argInt' => '3',
            'argMaybeFloat' => '3.14',
            'stringOrNull' => 'test',
            'date' => new IsoDate('2024-12-25'),
            'alias' => ['one', 'two'],
        ], $utils->validateGetParams(TestParams::class, [
            'argInt' => '3',
            'argMaybeFloat' => '3.14',
            'stringOrNull' => 'test',
            'date' => '2024-12-25',
            'alias' => ['one', 'two'],
        ]));
    }

    public function testValidateGetParamsNonNullableError(): void {
        $utils = new HttpUtilsForTest();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => null,
                'argMaybeFloat' => null,
                'stringOrNull' => null,
                'date' => '2024-12-25',
                'alias' => [],
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body);
        }
    }

    public function testValidateGetParamsMissingParamError(): void {
        $utils = new HttpUtilsForTest();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => '3',
                'argMaybeFloat' => null,
                'date' => '2024-12-25',
                'alias' => [],
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body);
        }
    }

    public function testValidateGetParamsRedundantParamError(): void {
        $utils = new HttpUtilsForTest();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => null,
                'argMaybeFloat' => null,
                'stringOrNull' => null,
                'date' => '2024-12-25',
                'alias' => [],
                'redundant' => 'yes',
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body);
        }
    }

    public function testValidateGetParamsEmptyError(): void {
        $utils = new HttpUtilsForTest();
        try {
            $utils->validateGetParams(TestParams::class, []);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body);
        }
    }
}
