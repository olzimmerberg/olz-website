<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AllCommandsCoveredTest extends UnitTestCase {
    public function testAllCommonCommandsCovered(): void {
        $command_path = __DIR__.'/../../../src/Command/';
        $this->assertTrue(is_dir($command_path));
        $command_realpath = realpath($command_path);
        $command_files = array_map(
            function ($path) use ($command_realpath) {
                return str_replace($command_realpath, '', $path);
            },
            array_merge(
                glob("{$command_realpath}/*.php"),
                glob("{$command_realpath}/**/*.php"),
            ),
        );
        $commands = array_filter(
            $command_files,
            function ($filename) {
                return preg_match('/\.php$/', $filename);
            },
        );
        $this->assertGreaterThan(0, count($commands));
        foreach ($commands as $command) {
            $res = preg_match('/^([a-zA-Z0-9\/]+)\.php$/', $command, $matches);
            $expected_test_path = __DIR__."/{$matches[1]}Test.php";
            $this->assertSame(1, $res);
            $this->assertTrue(is_file($expected_test_path), "Expected test for {$command}");
        }
    }
}
