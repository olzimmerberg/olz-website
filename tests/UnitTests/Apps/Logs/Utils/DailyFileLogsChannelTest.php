<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Logs\Utils;

use Olz\Apps\Logs\Utils\BaseLogsChannel;
use Olz\Apps\Logs\Utils\DailyFileLogsChannel;
use Olz\Apps\Logs\Utils\HybridLogFile;
use Olz\Apps\Logs\Utils\LogFileInterface;
use Olz\Apps\Logs\Utils\PlainLogFile;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

class TestOnlyDailyFileLogsChannel extends DailyFileLogsChannel {
    public static function getId(): string {
        return 'daily-file-logs-channel-id';
    }

    public static function getName(): string {
        return 'DailyFileLogsChannel name';
    }

    public function getRetentionDays(): ?int {
        return 2;
    }

    protected function getLogFileForDateTime(\DateTime $datetime): LogFileInterface {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $formatted = $datetime->format('Y-m-d');
        $plain_path = "{$logs_path}{$formatted}.log";
        $gz_path = "{$plain_path}.gz";
        $index_path = "{$logs_path}{$formatted}";
        if (is_file($plain_path)) {
            return new PlainLogFile($plain_path, $index_path);
        }
        if (is_file($gz_path)) {
            return new HybridLogFile($gz_path, $plain_path, $index_path);
        }
        throw new \Exception("No such file: {$plain_path} / {$gz_path}");
    }

    protected function getDateTimeForFilePath(string $file_path): \DateTime {
        $private_path = $this->envUtils()->getPrivatePath();
        $logs_path = "{$private_path}logs/";
        $esc_logs_path = preg_quote($logs_path, '/');
        $pattern = "/^{$esc_logs_path}(\\d{4}\\-\\d{2}\\-\\d{2})\\.log(\\.gz)?$/";
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
        $channel = new TestOnlyDailyFileLogsChannel();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $channel->setSession($session);

        $num_fake_on_page = intval(BaseLogsChannel::$pageSize / 2 - 3);
        $num_fake = intval(BaseLogsChannel::$pageSize * 2 / 3);
        mkdir(__DIR__.'/../../../tmp/private/logs/');
        $fake_content = [];
        for ($i = 0; $i < $num_fake; $i++) {
            $iso_date = date('Y-m-d H:i:s', strtotime('2020-03-12') + $i * 600);
            $fake_content[] = "[{$iso_date}] tick 2020-03-12\n";
        }
        $gzp = gzopen(__DIR__.'/../../../tmp/private/logs/2020-03-12.log.gz', 'wb');
        assert(!is_bool($gzp));
        gzwrite($gzp, implode('', $fake_content), $num_fake * 1024);
        gzclose($gzp);
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/2020-03-13.log',
            implode('', [
                "[2020-03-13 12:00:00] tick 2020-03-13\n",
                "[2020-03-13 14:00:00] OlzEndpoint.WARNING test log entry I\n",
                "[2020-03-13 18:00:00] OlzEndpoint.INFO test log entry II\n",
                "[2020-03-13 19:30:00] OlzEndpoint.INFO test log entry III\n",
            ]),
        );
        file_put_contents(
            __DIR__.'/../../../tmp/private/logs/2020-03-14.log',
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
            'DEBUG Create new index private-path/logs/2020-03-13.index.json.gz',
            'DEBUG log_file_before private-path/logs/2020-03-12.log.gz',
            'DEBUG Create new index private-path/logs/2020-03-12.index.json.gz',
            'DEBUG Cache hybrid log file private-path/logs/2020-03-12.log.gz -> private-path/logs/2020-03-12.log',
            'DEBUG Remove redundant hybrid log file private-path/logs/2020-03-12.log',
            'DEBUG log_file_after private-path/logs/2020-03-14.log',
            'DEBUG Create new index private-path/logs/2020-03-14.index.json.gz',
        ], $this->getLogs());
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
            '/\/tmp\/private\/logs\/2020-03-12\.log\.gz$/',
            $result->previous?->logFile->getPath() ?? '',
        );
        $this->assertMatchesRegularExpression(
            '/\/tmp\/private\/logs\/2020-03-12$/',
            $result->previous?->logFile->getIndexPath() ?? '',
        );
        $this->assertSame($num_fake - $num_fake_on_page - 1, $result->previous?->lineNumber);
        $this->assertNull($result->next);
    }
}
