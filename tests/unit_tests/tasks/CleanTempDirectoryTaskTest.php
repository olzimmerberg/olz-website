<?php

declare(strict_types=1);

require_once __DIR__.'/../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../_/tasks/CleanTempDirectoryTask.php';
require_once __DIR__.'/../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class FakeCleanTempDirectoryTask extends CleanTempDirectoryTask {
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
 * @covers \CleanTempDirectoryTask
 */
final class CleanTempDirectoryTaskTest extends UnitTestCase {
    public function testCleanTempDirectoryTaskErrorOpening(): void {
        $env_utils = new FakeEnvUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();
        $data_path = $env_utils->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        $temp_realpath = realpath($temp_path);

        $job = new FakeCleanTempDirectoryTask($date_utils, $env_utils);
        $job->opendir_override_result = false;
        $job->setLogger($logger);
        $job->run();

        $this->assertEqualsCanonicalizing([], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([], $job->unlink_calls);
        $this->assertSame([
            'INFO Setup task CleanTempDirectory...',
            'INFO Running task CleanTempDirectory...',
            'WARNING Failed to open directory data-path/temp',
            'INFO Finished task CleanTempDirectory.',
            'INFO Teardown task CleanTempDirectory...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testCleanTempDirectoryTaskRemovesEverything(): void {
        $env_utils = new FakeEnvUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();
        $data_path = $env_utils->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        mkdir("{$temp_path}/dir");
        file_put_contents("{$temp_path}/dir/file.txt", "test");
        file_put_contents("{$temp_path}/file.txt", "test");
        $temp_realpath = realpath($temp_path);

        $job = new FakeCleanTempDirectoryTask($date_utils, $env_utils);
        $job->setLogger($logger);
        $job->run();

        $this->assertEqualsCanonicalizing([
            "{$temp_realpath}/dir",
        ], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([
            "{$temp_realpath}/dir/file.txt",
            "{$temp_realpath}/file.txt",
        ], $job->unlink_calls);
        $this->assertSame([
            'INFO Setup task CleanTempDirectory...',
            'INFO Running task CleanTempDirectory...',
            'INFO Finished task CleanTempDirectory.',
            'INFO Teardown task CleanTempDirectory...',
        ], $logger->handler->getPrettyRecords());
    }

    public function testCleanTempDirectoryTaskRemoveNotYet(): void {
        $env_utils = new FakeEnvUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();
        $data_path = $env_utils->getDataPath();
        $temp_path = "{$data_path}temp/";
        mkdir($temp_path);
        mkdir("{$temp_path}/dir");
        file_put_contents("{$temp_path}/dir/file.txt", "test");
        file_put_contents("{$temp_path}/file.txt", "test");
        $temp_realpath = realpath($temp_path);

        $job = new FakeCleanTempDirectoryTask($date_utils, $env_utils);
        $job->filemtime_response = strtotime('2020-03-13 19:30:00');
        $job->setLogger($logger);
        $job->run();

        $this->assertEqualsCanonicalizing([], $job->rmdir_calls);
        $this->assertEqualsCanonicalizing([], $job->unlink_calls);
        $this->assertSame([
            'INFO Setup task CleanTempDirectory...',
            'INFO Running task CleanTempDirectory...',
            'INFO Finished task CleanTempDirectory.',
            'INFO Teardown task CleanTempDirectory...',
        ], $logger->handler->getPrettyRecords());
    }
}
