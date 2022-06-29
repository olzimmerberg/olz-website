<?php

declare(strict_types=1);

use Olz\Apps\OlzApps;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class AllAppsEndpointsCoveredTest extends UnitTestCase {
    public function testAllCommonEndpointsCovered(): void {
        $app_paths = OlzApps::getAppPaths();

        $app_paths_with_endpoints = [];
        foreach ($app_paths as $app_path) {
            if (is_dir("{$app_path}/Endpoints")) {
                $app_paths_with_endpoints[] = $app_path;
            }
        }

        $this->assertGreaterThan(0, count($app_paths_with_endpoints));

        foreach ($app_paths_with_endpoints as $app_path) {
            $app_basename = basename($app_path);
            $endpoints_files = scandir("{$app_path}/Endpoints");
            $endpoints = array_filter(
                $endpoints_files,
                function ($filename) {
                    return preg_match('/Endpoint\.php$/', $filename);
                },
            );
            $tests_dir = __DIR__."/{$app_basename}/Endpoints/";
            foreach ($endpoints as $endpoint) {
                $this->assertGreaterThan(0, count($endpoints));
                foreach ($endpoints as $endpoint) {
                    $res = preg_match('/^([a-zA-Z0-9]+)Endpoint\.php$/', $endpoint, $matches);
                    $expected_test_path = "{$tests_dir}{$matches[1]}EndpointTest.php";
                    $this->assertSame(1, $res);
                    $this->assertTrue(is_file($expected_test_path), "Expected test for {$endpoint}");
                }
            }
        }
    }
}
