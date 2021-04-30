<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/client/HttpUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @coversNothing
 */
class HttpUtilsForTest extends HttpUtils {
    public $sent_http_response_code;
    public $sent_http_header_lines = [];
    public $sent_http_body;
    public $has_exited_execution = false;

    protected function sendHttpResponseCode($http_response_code) {
        $this->sent_http_response_code = $http_response_code;
    }

    protected function sendHeader($http_header_line) {
        $this->sent_http_header_lines[] = $http_header_line;
    }

    protected function sendHttpBody($http_body) {
        $this->sent_http_body = $http_body;
    }

    protected function exitExecution() {
        $this->has_exited_execution = true;
    }

    public static function fromEnv() {
        return new self();
    }
}

/**
 * @internal
 * @covers \HttpUtils
 */
final class HttpUtilsIntegrationTest extends IntegrationTestCase {
    public function testHttpUtilsError(): void {
        $http_utils = HttpUtilsForTest::fromEnv();

        $http_utils->dieWithHttpError(404);

        $this->assertSame(404, $http_utils->sent_http_response_code);
        $this->assertSame([], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Fehler/i', $http_utils->sent_http_body);
        $this->assertSame(true, $http_utils->has_exited_execution);
    }

    public function testHttpUtilsRedirect(): void {
        $http_utils = HttpUtilsForTest::fromEnv();

        $http_utils->redirect('https://test.ch', 302);

        $this->assertSame(302, $http_utils->sent_http_response_code);
        $this->assertSame(["Location: https://test.ch"], $http_utils->sent_http_header_lines);
        $this->assertMatchesRegularExpression('/Weiterleitung/i', $http_utils->sent_http_body);
        $this->assertSame(true, $http_utils->has_exited_execution);
    }

    public function testValidateGetParamsSuccessful(): void {
        $http_utils = HttpUtilsForTest::fromEnv();

        $validated_get_params = $http_utils->validateGetParams([
            new Field('input', ['allow_null' => false]),
        ], ['input' => 'test']);

        $this->assertSame(['input' => 'test'], $validated_get_params);
    }

    public function testValidateGetParamsWithError(): void {
        $logger = new Logger('HttpUtilsIntegrationTest');
        $http_utils = HttpUtilsForTest::fromEnv();
        $http_utils->setLogger($logger);

        $validated_get_params = $http_utils->validateGetParams([
            new Field('input', ['allow_null' => false]),
        ], ['input' => null]);

        $this->assertSame([], $validated_get_params);
        // TODO: Uncomment this, once we are sure we know all the GET variables.
        // $this->assertSame(400, $http_utils->sent_http_response_code);
        // $this->assertSame([], $http_utils->sent_http_header_lines);
        // $this->assertMatchesRegularExpression('/Fehler/i', $http_utils->sent_http_body);
        // $this->assertSame(true, $http_utils->has_exited_execution);
    }

    public function testHttpUtilsFromEnv(): void {
        $http_utils = HttpUtils::fromEnv();

        $this->assertSame(false, !$http_utils);
    }
}
