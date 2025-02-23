<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanTempDirectoryCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeCleanTempDirectoryCommand extends CleanTempDirectoryCommand {
    /** @var bool|resource|null */
    public mixed $opendir_override_result = null;

    public ?int $filemtime_response = null;

    /** @var array<string> */
    public array $rmdir_calls = [];
    /** @var array<string> */
    public array $unlink_calls = [];

    /** @return bool|resource */
    protected function opendir(string $path): mixed {
        if ($this->opendir_override_result !== null) {
            return $this->opendir_override_result;
        }
        return parent::opendir($path);
    }

    protected function filemtime(string $path): bool|int {
        if ($this->filemtime_response !== null) {
            return $this->filemtime_response;
        }
        return 0;
    }

    protected function filectime(string $path): bool|int {
        return 0;
    }

    protected function rmdir(string $path): void {
        $this->rmdir_calls[] = $path;
    }

    protected function unlink(string $path): void {
        $this->unlink_calls[] = $path;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\CleanTempDirectoryCommand
 */
final class CleanTempDirectoryCommandTest extends UnitTestCase {
    public function testCleanTempDirectoryCommandErrorOpening(): void {
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanTempDirectoryCommand();
        $job->opendir_override_result = false;
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand...',
            'WARNING Failed to open directory data-path/temp',
            'INFO Successfully ran command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand.',
        ], $this->getLogs());

        $this->assertEqualsCanonicalizing([], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([], $job->unlink_calls);
    }

    public function testCleanTempDirectoryCommandRemovesEverything(): void {
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        mkdir("{$temp_path}/dir");
        file_put_contents("{$temp_path}/dir/file.txt", "test");
        file_put_contents("{$temp_path}/file.txt", "test");
        $temp_realpath = realpath($temp_path);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanTempDirectoryCommand();
        $job->run($input, $output);

        $this->assertEqualsCanonicalizing([
            "{$temp_realpath}/dir",
        ], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([
            "{$temp_realpath}/dir/file.txt",
            "{$temp_realpath}/file.txt",
        ], $job->unlink_calls);
        $this->assertSame([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand...',
            'INFO Successfully ran command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand.',
        ], $this->getLogs());
    }

    public function testCleanTempDirectoryCommandRemoveNotYet(): void {
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        mkdir("{$temp_path}/dir");
        file_put_contents("{$temp_path}/dir/file.txt", "test");
        file_put_contents("{$temp_path}/file.txt", "test");
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new FakeCleanTempDirectoryCommand();
        $job->filemtime_response = strtotime('2020-03-13 19:30:00');
        $job->run($input, $output);

        $this->assertEqualsCanonicalizing([], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([], $job->unlink_calls);
        $this->assertSame([
            'INFO Running command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand...',
            'INFO Successfully ran command Olz\Tests\UnitTests\Command\FakeCleanTempDirectoryCommand.',
        ], $this->getLogs());
    }
}
