<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Tests\Fake;
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

    protected function getFilePathBefore(string $path): string {
        $date_time = $this->getDateTimeForFilePath($path);
        $minus_one_day = \DateInterval::createFromDateString("-1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_before = (new \DateTime($iso_noon))->add($minus_one_day);
        $file_path = $this->getFilePathForDateTime($day_before);
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return $file_path;
    }

    protected function getFilePathAfter(string $path): string {
        $date_time = $this->getDateTimeForFilePath($path);
        $plus_one_day = \DateInterval::createFromDateString("+1 days");
        $iso_noon = $date_time->format('Y-m-d').' 12:00:00';
        $day_after = (new \DateTime($iso_noon))->add($plus_one_day);
        $file_path = $this->getFilePathForDateTime($day_after);
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return $file_path;
    }

    protected function getFilePathForDateTime(\DateTime $datetime): string {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
        $formatted = $datetime->format('Y-m-d');
        $file_path = "{$logs_path}{$formatted}.log";
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return $file_path;
    }

    protected function getDateTimeForFilePath(string $file_path): \DateTime {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
        $esc_logs_path = preg_quote($logs_path, '/');
        $pattern = "/^{$esc_logs_path}(\\d{4}\\-\\d{2}\\-\\d{2})\\.log$/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        $iso_date = $matches[1];
        return new \DateTime($iso_date);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Utils\BaseLogsChannel
 */
final class BaseLogsChannelTest extends UnitTestCase {
    public function testBaseLogsChannelTargetDate(): void {
        $logger = Fake\FakeLogger::create();
        $channel = new BaseLogsChannelForTest();
        $env_utils = new Fake\FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $channel->setEnvUtils($env_utils);
        $channel->setSession($session);
        $channel->setLog($logger);

        mkdir(__DIR__.'/../../../tmp/logs/');
        $fake_content = [];
        for ($i = 0; $i < 3600; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 60);
            $fake_content[] = "[{$iso_date}] tick 2020-03-12\n";
        }
        file_put_contents(
            __DIR__.'/../../../tmp/logs/2020-03-12.log',
            implode('', $fake_content),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/2020-03-13.log',
            implode('', [
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            ]),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/logs/2020-03-14.log',
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        );

        $result = $channel->readLogs([
            'targetDate' => '2020-03-13 18:30:00',
            'firstDate' => null,
            'lastDate' => null,
            'minLogLevel' => null,
            'textSearch' => null,
            'pageToken' => null,
        ]);

        $this->assertSame([
            'INFO BinarySearch: 1 38 2020-03-13 18:30:00 <=> 2020-03-13 14:00:00 -> 1',
            'INFO BinarySearch: 2 97 2020-03-13 18:30:00 <=> 2020-03-13 18:00:00 -> 1',
            'INFO file_path_before data-path/logs/2020-03-12.log',
            'INFO file_path_after data-path/logs/2020-03-14.log',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            ...array_slice($fake_content, 3600 - 997, 997),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            '---',
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        ], $result->lines);
        $this->assertMatchesRegularExpression(
            '/\/tmp\/logs\/2020-03-12.log$/',
            $result->previous->filePath,
        );
        $this->assertSame(2602, $result->previous->lineNumber);
        $this->assertSame(null, $result->next);
    }
}
