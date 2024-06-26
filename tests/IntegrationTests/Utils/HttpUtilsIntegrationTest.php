<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\FieldUtils;

/**
 * @internal
 *
 * @coversNothing
 */
class HttpUtilsForIntegrationTest extends HttpUtils {
    public int $sent_http_response_code;
    /** @var array<string> */
    public array $sent_http_header_lines = [];
    public string $sent_http_body;
    public bool $has_exited_execution = false;

    protected function sendHttpResponseCode(int $http_response_code): void {
        $this->sent_http_response_code = $http_response_code;
    }

    protected function sendHeader(string $http_header_line): void {
        $this->sent_http_header_lines[] = $http_header_line;
    }

    protected function sendHttpBody(string $http_body): void {
        $this->sent_http_body = $http_body;
    }

    protected function exitExecution(): void {
        $this->has_exited_execution = true;
    }

    public static function fromEnv(): self {
        $http_utils = new self();
        $field_utils = FieldUtils::create();
        $http_utils->setFieldUtils($field_utils);
        return $http_utils;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsIntegrationTest extends IntegrationTestCase {
    public function testHttpUtilsError(): void {
        $http_utils = HttpUtilsForIntegrationTest::fromEnv();

        $http_utils->dieWithHttpError(404);

        $this->assertSame(404, $http_utils->sent_http_response_code);
        $this->assertSame([], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Fehler/i', $http_utils->sent_http_body);
        $this->assertTrue($http_utils->has_exited_execution);
    }

    public function testHttpUtilsRedirect(): void {
        $http_utils = HttpUtilsForIntegrationTest::fromEnv();

        $http_utils->redirect('https://test.ch', 302);

        $this->assertSame(302, $http_utils->sent_http_response_code);
        $this->assertSame(["Location: https://test.ch"], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Weiterleitung/i', $http_utils->sent_http_body);
        $this->assertTrue($http_utils->has_exited_execution);
    }

    public function testValidateGetParamsSuccessful(): void {
        $http_utils = HttpUtilsForIntegrationTest::fromEnv();

        $validated_get_params = $http_utils->validateGetParams([
            'input' => new FieldTypes\Field(['allow_null' => false]),
        ], ['input' => 'test']);

        $this->assertSame(['input' => 'test'], $validated_get_params);
    }

    public function testValidateGetParamsWithBadParam(): void {
        $http_utils = HttpUtilsForIntegrationTest::fromEnv();

        $validated_get_params = $http_utils->validateGetParams([
            'input' => new FieldTypes\Field(['allow_null' => false]),
        ], ['input' => null]);

        $this->assertSame([
            "NOTICE Bad GET param 'input'",
        ], $this->getLogs());
        $this->assertSame([], $validated_get_params);
        $this->assertSame(400, $http_utils->sent_http_response_code);
        $this->assertSame([], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Fehler/i', $http_utils->sent_http_body);
        $this->assertTrue($http_utils->has_exited_execution);
    }

    public function testValidateGetParamsWithUnknownParam(): void {
        $http_utils = HttpUtilsForIntegrationTest::fromEnv();

        $validated_get_params = $http_utils->validateGetParams(
            [],
            ['inexistent' => null]
        );

        $this->assertSame([
            "NOTICE Unknown GET param 'inexistent'",
        ], $this->getLogs());
        $this->assertSame([], $validated_get_params);
        $this->assertSame(400, $http_utils->sent_http_response_code);
        $this->assertSame([], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Fehler/i', $http_utils->sent_http_body);
        $this->assertTrue($http_utils->has_exited_execution);
    }
}
