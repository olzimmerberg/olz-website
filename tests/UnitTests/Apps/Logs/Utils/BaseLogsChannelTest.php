<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\LogFileInterface;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class TestOnlyBaseLogsChannel extends BaseLogsChannel {
    public static function getId(): string {
        return 'base-logs-channel-id';
    }

    public static function getName(): string {
        return 'BaseLogsChannel name';
    }

    protected function getLineLocationForDateTime(
        \DateTime $date_time,
    ): LineLocation {
        $realpath = realpath(__DIR__.'/../../../tmp/private/logs/fake.log');
        assert($realpath);
        $index_path = "{$realpath}.index.json.gz";
        $log_file = new PlainLogFile($realpath, $index_path);
        return new LineLocation($log_file, 1, 0);
    }

    protected function getLogFileBefore(LogFileInterface $log_file): LogFileInterface {
        if (preg_match('/fake-before\.log$/', $log_file->getPath())) {
            throw new \Exception("No such file: {$log_file->getPath()}");
        }
        $realpath = realpath(__DIR__.'/../../../tmp/private/logs/fake-before.log');
        assert($realpath);
        $index_path = "{$realpath}.index.json.gz";
        return new PlainLogFile($realpath, $index_path);
    }

    protected function getLogFileAfter(LogFileInterface $log_file): LogFileInterface {
        if (preg_match('/fake-after\.log$/', $log_file->getPath())) {
            throw new \Exception("No such file: {$log_file->getPath()}");
        }
        $realpath = realpath(__DIR__.'/../../../tmp/private/logs/fake-after.log');
        assert($realpath);
        $index_path = "{$realpath}.index.json.gz";
        return new PlainLogFile($realpath, $index_path);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Utils\BaseLogsChannel
 */
final class BaseLogsChannelTest extends UnitTestCase {
    public function testBaseLogsChannelTargetDate(): void {
        $channel = new TestOnlyBaseLogsChannel();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];

        mkdir(__DIR__.'/../../../tmp/private/logs/');
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/fake-before.log',
            "[2020-03-12 12:00:00] tick 2020-03-12 \xc3\xb1 ~*%&*)(öä\n", // valid UTF-8
        );
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/fake.log',
            "[2020-03-13 12:00:00] tick 2020-03-13 \xc3\x28\n", // invalid UTF-8
        );
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/fake-after.log',
            "[2020-03-14 12:00:00] tick 2020-03-14 \xf0\x28\x8c\x28\n", // invalid UTF-8
        );

        $date_time = new \DateTime('2020-03-13 18:30:00');
        $result = $channel->readAroundDateTime($date_time, [
            'targetDate' => '2020-03-13 18:30:00',
            'firstDate' => null,
            'lastDate' => null,
            'minLogLevel' => null,
            'textSearch' => null,
            'pageToken' => null,
        ]);

        $this->assertSame([
            'DEBUG Create new index data-realpath//private/logs/fake.log.index.json.gz',
            'DEBUG log_file_before data-realpath//private/logs/fake-before.log',
            'DEBUG Create new index data-realpath//private/logs/fake-before.log.index.json.gz',
            'DEBUG log_file_after data-realpath//private/logs/fake-after.log',
            'DEBUG Create new index data-realpath//private/logs/fake-after.log.index.json.gz',
        ], $this->getLogs());
        $this->assertSame([
            "[2020-03-12 12:00:00] tick 2020-03-12 \xc3\xb1 ~*%&*)(öä\n",
            "[2020-03-13 12:00:00] tick 2020-03-13 (\n",
            "---",
            "[2020-03-14 12:00:00] tick 2020-03-14 ((\n",
        ], $result->lines);
        $this->assertNull($result->previous);
        $this->assertNull($result->next);
    }
}
