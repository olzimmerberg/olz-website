<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\HttpUtils;

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
        return new self();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsIntegrationTest extends IntegrationTestCase {
    public function testHttpUtilsError(): void {
        $utils = $this->getSut();

        $utils->dieWithHttpError(404);

        $this->assertSame(404, $utils->sent_http_response_code);
        $this->assertSame([], $utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Fehler/i', $utils->sent_http_body);
        $this->assertTrue($utils->has_exited_execution);
    }

    public function testHttpUtilsRedirect(): void {
        $utils = $this->getSut();

        $utils->redirect('https://test.ch', 302);

        $this->assertSame(302, $utils->sent_http_response_code);
        $this->assertSame(["Location: https://test.ch"], $utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Weiterleitung/i', $utils->sent_http_body);
        $this->assertTrue($utils->has_exited_execution);
    }

    protected function getSut(): HttpUtilsForIntegrationTest {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(HttpUtilsForIntegrationTest::class);
    }
}
