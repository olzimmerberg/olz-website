<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\GzLogFile;
use Olz\Apps\Logs\Utils\LogFileInterface;
use Olz\Apps\Logs\Utils\LogrotateLogsChannel;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class TestOnlyLogrotateLogsChannel extends LogrotateLogsChannel {
    public static function getId(): string {
        return 'logrotate-logs-channel-id';
    }

    public static function getName(): string {
        return 'LogrotateLogsChannel name';
    }

    protected function getLogFileForIndex(int $index): LogFileInterface {
        $log_name = 'syslog';
        $syslog_path = $this->envUtils()->getSyslogPath();
        $base_index = $index - 1;
        $basename = "{$log_name}.processed.{$base_index}";
        $file_path = "{$syslog_path}{$basename}";
        if ($index === 0) {
            $file_path = "{$syslog_path}{$log_name}";
        }
        if ($index === 1) {
            $file_path = "{$syslog_path}{$log_name}.processed";
        }
        $index_path = "{$file_path}.index.json.gz";
        if (is_file($file_path)) {
            return new PlainLogFile($file_path, $index_path);
        }
        if (is_file("{$file_path}.gz")) {
            return new GzLogFile("{$file_path}.gz", $index_path);
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
            return 0;
        }
        if ($matches[1] === '.processed') {
            return 1;
        }
        $index = intval($matches[2]) + 1;
        if ($index < 0) {
            throw new \Exception("Index is below 0. This should never happen, due to the regex.");
        }
        return $index;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Logs\Utils\LogrotateLogsChannel
 */
final class LogrotateLogsChannelTest extends UnitTestCase {
    public function testLogrotateLogsChannelTargetDate(): void {
        $channel = new TestOnlyLogrotateLogsChannel();
        WithUtilsCache::get('session')->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];

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
            'DEBUG Create new index data-path/syslog/syslog.index.json.gz',
            'DEBUG Create new index data-path/syslog/syslog.processed.index.json.gz',
            'DEBUG Create new index data-path/syslog/syslog.processed.1.index.json.gz',
            'DEBUG log_file_before data-path/syslog/syslog.processed.2.gz',
            'DEBUG Create new index data-path/syslog/syslog.processed.2.index.json.gz',
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
            '/\/tmp\/syslog\/syslog\.processed\.2\.gz$/',
            $result->previous?->logFile->getPath() ?? '',
        );
        $this->assertMatchesRegularExpression(
            '/\/tmp\/syslog\/syslog\.processed\.2\.index\.json\.gz$/',
            $result->previous?->logFile->getIndexPath() ?? '',
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $result->previous?->lineNumber);
        $this->assertNull($result->next);
    }
}
