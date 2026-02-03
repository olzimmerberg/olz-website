<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanLogsCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\CleanLogsCommand
 */
final class CleanLogsCommandTest extends UnitTestCase {
    public function testCleanLogsCommandCleansUp(): void {
        $private_path = WithUtilsCache::get('envUtils')->getPrivatePath();
        mkdir("{$private_path}logs/", 0o777, true);
        file_put_contents("{$private_path}logs/merged-2019-03-12.log", "test");
        file_put_contents("{$private_path}logs/merged-2019-03-13.log", "test");
        file_put_contents("{$private_path}logs/merged-2020-03-12.log", "test");
        file_put_contents("{$private_path}logs/merged-2020-03-13.log", "test");

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new CleanLogsCommand();
        $result = $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\CleanLogsCommand...',
            'INFO Cleaning logs channel OLZ Logs (olz-logs)...',
            'INFO Optimizing last 366 hybrid log files in channel OLZ Logs (olz-logs)...',
            'DEBUG Optimize hybrid log file private-path/logs/merged-2020-03-12.log -> private-path/logs/merged-2020-03-12.log.gz',
            'DEBUG Remove redundant hybrid log file private-path/logs/merged-2020-03-12.log',
            'DEBUG Optimize hybrid log file private-path/logs/merged-2019-03-13.log -> private-path/logs/merged-2019-03-13.log.gz',
            'DEBUG Remove redundant hybrid log file private-path/logs/merged-2019-03-13.log',
            'INFO Clean up 30 log files before 2019-03-13 in channel OLZ Logs (olz-logs)...',
            'INFO Removed old plain log file private-path/logs/merged-2019-03-12.log',
            'INFO Nothing to do cleaning logs channel Error Logs (error-logs).',
            'INFO Nothing to do cleaning logs channel Access SSL Logs (access-ssl-logs).',
            'INFO Successfully ran command Olz\Command\CleanLogsCommand.',
        ], $this->getLogs(function ($record, $level_name, $message) {
            if ($level_name === 'DEBUG' && preg_match('/^Optimizing [a-zA-Z\\\]+ for day [0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $message)) {
                return null;
            }
            if ($level_name === 'DEBUG' && preg_match('/^Optimizing hybrid log file private\-path\/logs\/merged\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}\.log (?:✅|🚫) \/ private\-path\/logs\/merged\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}\.log\.gz (?:✅|🚫)\.\.\.$/', $message)) {
                return null;
            }
            return "{$level_name} {$message}";
        }));

        $this->assertFileDoesNotExist("{$private_path}logs/merged-2019-03-12.log");
        $this->assertFileDoesNotExist("{$private_path}logs/merged-2019-03-13.log");
        $this->assertFileExists("{$private_path}logs/merged-2019-03-13.log.gz");
        $this->assertFileDoesNotExist("{$private_path}logs/merged-2020-08-12.log");
        $this->assertFileExists("{$private_path}logs/merged-2020-03-12.log.gz");
        $this->assertFileDoesNotExist("{$private_path}logs/merged-2020-08-15.log");
        $this->assertFileExists("{$private_path}logs/merged-2020-03-13.log");

        $this->assertSame(Command::SUCCESS, $result);
    }
}
