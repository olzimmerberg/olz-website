<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Api;

use Olz\Api\OlzApi;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use PhpTypeScriptApi\Endpoint;

/**
 * @internal
 * covers different Classes!
 *
 * @coversNothing
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = OlzApi::getInstance();

        foreach ($olz_api->getEndpointNames() as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint->setup();
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
