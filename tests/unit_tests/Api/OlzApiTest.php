<?php

declare(strict_types=1);

use Olz\Api\OlzApi;
use PhpTypeScriptApi\Endpoint;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class OlzApiTest extends UnitTestCase {
    public function testOlzApiHasEndpoints(): void {
        $olz_api = OlzApi::getInstance();
        $this->assertGreaterThan(0, count($olz_api->getEndpointNames()));
    }

    public function testOlzApiCanCreateAllEndpoints(): void {
        $olz_api = OlzApi::getInstance();
        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $this->assertTrue($endpoint instanceof Endpoint);
        }
    }

    public function testOlzApiHasBeenGenerated(): void {
        $actual_content = file_get_contents(__DIR__.'/../../../src/Api/client/generated_olz_api_types.ts');

        ob_start();
        OlzApi::generate();
        ob_end_clean();

        $expected_content = file_get_contents(__DIR__.'/../../../src/Api/client/generated_olz_api_types.ts');

        $this->assertSame($expected_content, $actual_content);
    }
}
