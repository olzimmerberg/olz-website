<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Api;

use Olz\Api\OlzApi;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use PhpTypeScriptApi\Endpoint;
use PhpTypeScriptApi\TypedEndpoint;
use Psr\Log\NullLogger;

/**
 * @internal
 * covers different Classes!
 *
 * @coversNothing
 */
final class OlzApiIntegrationTest extends IntegrationTestCase {
    public function testCanSetupEachEndpoint(): void {
        $olz_api = $this->getSut();

        $endpoint_names = $olz_api->getEndpointNames();
        $this->assertGreaterThan(0, count($endpoint_names));
        foreach ($endpoint_names as $endpoint_name) {
            $endpoint = $olz_api->getEndpointByName($endpoint_name);
            $endpoint?->setLogger(new NullLogger());
            $endpoint?->setup();
            $this->assertTrue(
                $endpoint instanceof Endpoint
                || $endpoint instanceof TypedEndpoint
            );
        }
    }

    public function testOlzApiHasBeenGenerated(): void {
        $actual_content = file_get_contents(__DIR__.'/../../../src/Api/client/generated_olz_api_types.ts');

        ob_start();
        $olz_api = $this->getSut();
        $olz_api->generate();
        ob_end_clean();

        $expected_content = file_get_contents(__DIR__.'/../../../src/Api/client/generated_olz_api_types.ts');

        $this->assertSame($expected_content, $actual_content);
    }

    protected function getSut(): OlzApi {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(OlzApi::class);
    }
}
