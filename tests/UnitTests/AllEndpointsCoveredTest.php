<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests;

use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AllEndpointsCoveredTest extends UnitTestCase {
    public function testAllEndpointsCovered(): void {
        $src_path = __DIR__.'/../../src/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        assert($src_realpath);
        $endpoints_folders = [
            ...(glob("{$src_realpath}/Endpoints") ?: []),
            ...(glob("{$src_realpath}/*/Endpoints") ?: []),
            ...(glob("{$src_realpath}/*/*/Endpoints") ?: []),
        ] ?: [];
        foreach ($endpoints_folders as $endpoints_folder) {
            $endpoints_files = scandir($endpoints_folder) ?: [];
            $endpoints = array_filter(
                $endpoints_files,
                fn ($filename) => (bool) preg_match('/Endpoint\.php$/', $filename),
            );
            $endpoints_relative_path = substr($endpoints_folder, strlen($src_realpath));
            $this->assertGreaterThan(0, count($endpoints));
            foreach ($endpoints as $endpoint) {
                $res = preg_match('/^([a-zA-Z0-9]+)Endpoint\.php$/', $endpoint, $matches);
                $fileident = $matches[1] ?? '';
                $expected_test_path = "{$endpoints_relative_path}/{$fileident}EndpointTest.php";
                $this->assertSame(1, $res);
                $this->assertTrue(
                    is_file(__DIR__.$expected_test_path),
                    "Expected test for {$endpoint} at .../UnitTests{$expected_test_path}",
                );
            }
        }
    }
}
