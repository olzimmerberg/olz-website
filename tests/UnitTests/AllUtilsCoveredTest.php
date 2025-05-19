<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AllUtilsCoveredTest extends UnitTestCase {
    public function testAllUtilsCovered(): void {
        $src_path = __DIR__.'/../../src/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        assert($src_realpath);
        $utils_folders = [
            ...(glob("{$src_realpath}/Utils") ?: []),
            ...(glob("{$src_realpath}/*/Utils") ?: []),
        ] ?: [];
        foreach ($utils_folders as $utils_folder) {
            $utils_files = scandir($utils_folder) ?: [];
            $utils = array_filter(
                $utils_files,
                fn ($filename) => (bool) preg_match('/\.php$/', $filename),
            );
            $utils_relative_path = substr($utils_folder, strlen($src_realpath));
            $this->assertGreaterThan(0, count($utils));
            foreach ($utils as $util) {
                $res = preg_match('/^([a-zA-Z0-9]+)\.php$/', $util, $matches);
                $fileident = $matches[1] ?? '';
                $expected_test_path = "{$utils_relative_path}/{$fileident}Test.php";
                $this->assertSame(1, $res);
                $this->assertTrue(
                    is_file(__DIR__.$expected_test_path),
                    "Expected test for {$util} at .../UnitTests{$expected_test_path}",
                );
            }
        }
    }
}
