<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanTempDirectoryCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeCleanTempDirectoryCommand extends CleanTempDirectoryCommand {
    public $opendir_override_result;

    public $filemtime_response;

    public $rmdir_calls = [];
    public $unlink_calls = [];

    protected function opendir($path) {
        if ($this->opendir_override_result !== null) {
            return $this->opendir_override_result;
        }
        return parent::opendir($path);
    }

    protected function filemtime($path) {
        if ($this->filemtime_response !== null) {
            return $this->filemtime_response;
        }
        return 0;
    }

    protected function filectime($path) {
        return 0;
    }

    protected function rmdir($path) {
        $this->rmdir_calls[] = $path;
    }

    protected function unlink($path) {
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
        $temp_realpath = realpath($temp_path);
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
        $temp_realpath = realpath($temp_path);
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
