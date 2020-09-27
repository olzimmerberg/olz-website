<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class AllEndpointsCoveredTest extends TestCase {
    public function testAllEndpointsCovered(): void {
        $endpoints_path = __DIR__.'/../../../../src/api/endpoints';

        $this->assertTrue(is_dir($endpoints_path));
        $endpoints_files = scandir($endpoints_path);
        $endpoints = array_filter(
            $endpoints_files,
            function ($filename) {
                return preg_match('/Endpoint\.php$/', $filename);
            },
        );
        $this->assertGreaterThan(0, count($endpoints));
        foreach ($endpoints as $endpoint) {
            $res = preg_match('/^([a-zA-Z0-9]+)Endpoint\.php$/', $endpoint, $matches);
            $expected_test_path = __DIR__."/{$matches[1]}EndpointTest.php";
            $this->assertSame(1, $res);
            $this->assertTrue(is_file($expected_test_path), "Expected test for {$endpoint}");
        }
    }
}
