<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\LineLocation;
use Olz\Apps\Logs\Utils\LogFileInterface;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseLogsChannelForTest extends BaseLogsChannel {
    public static function getId(): string {
        return 'base-logs-channel-id';
    }

    public static function getName(): string {
        return 'BaseLogsChannel name';
    }

    protected function getLineLocationForDateTime(
        \DateTime $date_time,
    ): LineLocation {
        $log_file = new PlainLogFile(realpath(__DIR__.'/../../../tmp/logs/fake.log'));
        return new LineLocation($log_file, 1);
    }

    protected function getLogFileBefore(LogFileInterface $log_file): LogFileInterface {
        if (preg_match('/fake-before\.log$/', $log_file->getPath())) {
            throw new \Exception("No such file: {$log_file->getPath()}");
        }
        return new PlainLogFile(realpath(__DIR__.'/../../../tmp/logs/fake-before.log'));
    }

    protected function getLogFileAfter(LogFileInterface $log_file): LogFileInterface {
        if (preg_match('/fake-after\.log$/', $log_file->getPath())) {
            throw new \Exception("No such file: {$log_file->getPath()}");
        }
        return new PlainLogFile(realpath(__DIR__.'/../../../tmp/logs/fake-after.log'));
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Utils\BaseLogsChannel
 */
final class BaseLogsChannelTest extends UnitTestCase {
    public function testBaseLogsChannelTargetDate(): void {
        $channel = new BaseLogsChannelForTest();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $channel->setSession($session);

        mkdir(__DIR__.'/../../../tmp/logs/');
        file_put_contents(
            __DIR__.'/../../../tmp/logs/fake-before.log',
            "[2020-03-12 12:00:00] tick 2020-03-12 \xc3\xb1 ~*%&*)(öä\n", // valid UTF-8
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/fake.log',
            "[2020-03-13 12:00:00] tick 2020-03-13 \xc3\x28\n", // invalid UTF-8
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/fake-after.log',
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
            'DEBUG log_file_before data-realpath//logs/fake-before.log',
            'DEBUG log_file_after data-realpath//logs/fake-after.log',
        ], $this->getLogs());
        $this->assertSame([
            "[2020-03-12 12:00:00] tick 2020-03-12 \xc3\xb1 ~*%&*)(öä\n",
            "[2020-03-13 12:00:00] tick 2020-03-13 (\n",
            "---",
            "", // TODO: avoid this
            "[2020-03-14 12:00:00] tick 2020-03-14 ((\n",
        ], $result->lines);
        $this->assertSame(null, $result->previous);
        $this->assertSame(null, $result->next);
    }
}
