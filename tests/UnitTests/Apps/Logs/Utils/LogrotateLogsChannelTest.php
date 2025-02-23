<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\GzLogFile;
use Olz\Apps\Logs\Utils\LogFileInterface;
use Olz\Apps\Logs\Utils\LogrotateLogsChannel;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @coversNothing
 */
class LogrotateLogsChannelForTest extends LogrotateLogsChannel {
    public static function getId(): string {
        return 'logrotate-logs-channel-id';
    }

    public static function getName(): string {
        return 'LogrotateLogsChannel name';
    }

    protected function getLogFileForIndex(int $index): LogFileInterface {
        $log_name = 'syslog';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $basename = "{$log_name}.processed.{$index}";
        $file_path = "{$syslog_path}{$basename}";
        if ($index === -1) {
            $file_path = "{$syslog_path}{$log_name}";
        }
        if ($index === 0) {
            $file_path = "{$syslog_path}{$log_name}.processed";
        }
        if (is_file($file_path)) {
            return new PlainLogFile($file_path);
        }
        if (is_file("{$file_path}.gz")) {
            return new GzLogFile($file_path, "{$file_path}.gz");
        }
        throw new \Exception("No such file: {$file_path}");
    }

    protected function getIndexForFilePath(string $file_path): int {
        $log_name = 'syslog';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $esc_syslog_path = preg_quote($syslog_path, '/');
        $pattern = "/^{$esc_syslog_path}{$log_name}($|\\.processed$|\\.processed\\.(\\d+)$)/";
        $res = preg_match($pattern, $file_path, $matches);
        if (!$res) {
            throw new \Exception("Not an OLZ Log file path: {$file_path}");
        }
        if ($matches[1] === '') {
            return -1;
        }
        if ($matches[1] === '.processed') {
            return 0;
        }
        return intval($matches[2]);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Utils\LogrotateLogsChannel
 */
final class LogrotateLogsChannelTest extends UnitTestCase {
    public function testLogrotateLogsChannelTargetDate(): void {
        $channel = new LogrotateLogsChannelForTest();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $channel->setSession($session);

        $num_fake_on_page = intval(BaseLogsChannel::$pageSize / 2 - 3);
        $num_fake = intval(BaseLogsChannel::$pageSize * 2 / 3);
        mkdir(__DIR__.'/../../../tmp/syslog/');
        file_put_contents(
            __DIR__.'/../../../tmp/syslog/syslog',
            "[2020-03-15 12:00:00] tick 2020-03-15\n",
        );
        file_put_contents(
            __DIR__.'/../../../tmp/syslog/syslog.processed',
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
        );
        file_put_contents(
            __DIR__.'/../../../tmp/syslog/syslog.processed.1',
            implode('', [
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            ]),
        );
        $fake_content = [];
        for ($i = 0; $i < $num_fake; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 600);
            $fake_content[] = "[{$iso_date}] tick 2020-03-12\n";
        }
        file_put_contents(
            __DIR__.'/../../../tmp/syslog/syslog.processed.2.gz',
            gzencode(implode('', $fake_content)),
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
        // sleep(100);

        $this->assertSame([
            'DEBUG log_file_before data-path/syslog/syslog.processed.2',
            'DEBUG log_file_after data-path/syslog/syslog.processed',
            'DEBUG log_file_after data-path/syslog/syslog',
        ], $this->getLogs());
        $this->assertSame([
            ...array_slice($fake_content, $num_fake - $num_fake_on_page, $num_fake_on_page),
            "[2020-03-13 12:00:00] tick 2020-03-13\n",
            "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
            "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
            '---',
            "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            "[2020-03-14 12:00:00] tick 2020-03-14\n",
            "[2020-03-15 12:00:00] tick 2020-03-15\n",
        ], $result->lines);
        $this->assertMatchesRegularExpression(
            '/\/tmp\/syslog\/syslog.processed.2$/',
            $result->previous->logFile->getPath(),
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $result->previous->lineNumber);
        $this->assertNull($result->next);
    }
}
