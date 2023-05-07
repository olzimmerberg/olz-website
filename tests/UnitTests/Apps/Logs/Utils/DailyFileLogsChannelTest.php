<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\DailyFileLogsChannel;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @coversNothing
 */
class DailyFileLogsChannelForTest extends DailyFileLogsChannel {
    public static function getId(): string {
        return 'daily-file-logs-channel-id';
    }

    public static function getName(): string {
        return 'DailyFileLogsChannel name';
    }

    protected function getLogFileForDateTime(\DateTime $datetime): PlainLogFile {
        $data_path = $this->envUtils()->getDataPath();
        $logs_path = "{$data_path}logs/";
        $formatted = $datetime->format('Y-m-d');
        $file_path = "{$logs_path}{$formatted}.log";
        if (!is_file($file_path)) {
            throw new \Exception("No such file: {$file_path}");
        }
        return new PlainLogFile($file_path);
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
 * @covers \Olz\Apps\Logs\Utils\DailyFileLogsChannel
 */
final class DailyFileLogsChannelTest extends UnitTestCase {
    public function testDailyFileLogsChannelTargetDate(): void {
        $logger = Fake\FakeLogger::create();
        $channel = new DailyFileLogsChannelForTest();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $channel->setSession($session);
        $channel->setLog($logger);

        $num_fake_on_page = intval(BaseLogsChannel::$pageSize / 2 - 3);
        $num_fake = intval(BaseLogsChannel::$pageSize * 2 / 3);
        mkdir(__DIR__.'/../../../tmp/logs/');
        $fake_content = [];
        for ($i = 0; $i < $num_fake; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 600);
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
            'DEBUG log_file_before data-path/logs/2020-03-12.log',
            'DEBUG log_file_after data-path/logs/2020-03-14.log',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([
            ...array_slice($fake_content, $num_fake - $num_fake_on_page, $num_fake_on_page),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            '---',
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        ], $result->lines);
        $this->assertMatchesRegularExpression(
            '/\/tmp\/logs\/2020-03-12.log$/',
            $result->previous->logFile->getPath(),
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $result->previous->lineNumber);
        $this->assertSame(null, $result->next);
    }
}
