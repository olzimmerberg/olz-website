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
            'DEBUG Optimize hybrid log file private-path/logs/merged-2020-03-12.log -> private-path/logs/merged-2020-03-12.log.gz',
            'DEBUG Remove redundant hybrid log file private-path/logs/merged-2020-03-12.log',
            'DEBUG Optimize hybrid log file private-path/logs/merged-2019-03-13.log -> private-path/logs/merged-2019-03-13.log.gz',
            'DEBUG Remove redundant hybrid log file private-path/logs/merged-2019-03-13.log',
            'INFO Removed old plain log file private-path/logs/merged-2019-03-12.log',
            'INFO Successfully ran command Olz\Command\CleanLogsCommand.',
        ], $this->getLogs());

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
