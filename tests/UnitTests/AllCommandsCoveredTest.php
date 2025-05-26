<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests;

use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AllCommandsCoveredTest extends UnitTestCase {
    public function testAllCommandsCovered(): void {
        $src_path = __DIR__.'/../../src/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        assert($src_realpath);
        $commands_folders = [
            ...(glob("{$src_realpath}/Command") ?: []),
        ] ?: [];
        foreach ($commands_folders as $commands_folder) {
            $command_files = array_map(
                function ($path) use ($commands_folder) {
                    return str_replace($commands_folder, '', $path);
                },
                [
                    ...(glob("{$commands_folder}*.php") ?: []),
                    ...(glob("{$commands_folder}**/*.php") ?: []),
                ],
            );
            $commands = array_filter(
                $command_files,
                fn ($filename) => (bool) preg_match('/\.php$/', $filename),
            );
            $commands_relative_path = substr($commands_folder, strlen($src_realpath));
            $this->assertGreaterThan(0, count($commands));
            foreach ($commands as $command) {
                $res = preg_match('/^\/([a-zA-Z0-9\/]+)\.php$/', $command, $matches);
                $fileident = $matches[1] ?? '';
                $expected_test_path = "{$commands_relative_path}/{$fileident}Test.php";
                $this->assertSame(1, $res);
                $this->assertTrue(
                    is_file(__DIR__.$expected_test_path),
                    "Expected test for {$command} at .../UnitTests{$expected_test_path}",
                );
            }
        }
    }
}
