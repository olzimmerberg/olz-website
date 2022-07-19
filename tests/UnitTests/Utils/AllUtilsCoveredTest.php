<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 * @coversNothing
 */
final class AllUtilsCoveredTest extends UnitTestCase {
    public function testAllCommonUtilsCovered(): void {
        $utils_path = __DIR__.'/../../../src/Utils/';
        $this->assertTrue(is_dir($utils_path));
        $utils_realpath = realpath($utils_path);
        $utils_files = array_map(
            function ($path) use ($utils_realpath) {
                return str_replace($utils_realpath, '', $path);
            },
            array_merge(
                glob("{$utils_realpath}/*.php"),
                glob("{$utils_realpath}/**/*.php"),
            ),
        );
        $utils = array_filter(
            $utils_files,
            function ($filename) {
                return preg_match('/\.php$/', $filename);
            },
        );
        $this->assertGreaterThan(0, count($utils));
        foreach ($utils as $util) {
            $res = preg_match('/^([a-zA-Z0-9\/]+)\.php$/', $util, $matches);
            $expected_test_path = __DIR__."/{$matches[1]}Test.php";
            $this->assertSame(1, $res);
            $this->assertTrue(is_file($expected_test_path), "Expected test for {$util}");
        }
    }

    public function testAllModulesUtilsCovered(): void {
        $src_path = __DIR__.'/../../../_/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        $utils_folders = glob("{$src_realpath}/*/utils/");
        foreach ($utils_folders as $utils_folder) {
            $utils_files = scandir($utils_folder);
            $utils = array_filter(
                $utils_files,
                function ($filename) {
                    return preg_match('/\.php$/', $filename);
                },
            );
            $utils_relative_path = substr($utils_folder, strlen($src_realpath));
            $utils_test_folder = __DIR__."/..{$utils_relative_path}";
            $this->assertGreaterThan(0, count($utils));
            foreach ($utils as $util) {
                $res = preg_match('/^([a-zA-Z0-9]+)\.php$/', $util, $matches);
                $expected_test_path = $utils_test_folder."/{$matches[1]}Test.php";
                $this->assertSame(1, $res);
                $this->assertTrue(is_file($expected_test_path), "Expected test for {$utils_relative_path}{$util}");
            }
        }
    }
}
