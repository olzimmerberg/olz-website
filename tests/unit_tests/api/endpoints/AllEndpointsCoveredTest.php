<?php

declare(strict_types=1);

require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class AllEndpointsCoveredTest extends UnitTestCase {
    public function testAllCommonEndpointsCovered(): void {
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

    public function testAllModulesEndpointsCovered(): void {
        $src_path = __DIR__.'/../../../../src/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        $endpoints_folders = glob("{$src_realpath}/*/endpoints/");
        foreach ($endpoints_folders as $endpoints_folder) {
            $endpoints_files = scandir($endpoints_folder);
            $endpoints = array_filter(
                $endpoints_files,
                function ($filename) {
                    return preg_match('/Endpoint\.php$/', $filename);
                },
            );
            $endpoints_relative_path = substr($endpoints_folder, strlen($src_realpath));
            $endpoints_test_folder = __DIR__."/../..{$endpoints_relative_path}";
            $this->assertGreaterThan(0, count($endpoints));
            foreach ($endpoints as $endpoint) {
                $res = preg_match('/^([a-zA-Z0-9]+)Endpoint\.php$/', $endpoint, $matches);
                $expected_test_path = $endpoints_test_folder."/{$matches[1]}EndpointTest.php";
                $this->assertSame(1, $res);
                $this->assertTrue(is_file($expected_test_path), "Expected test for {$endpoints_relative_path}{$endpoint}");
            }
        }
    }
}
