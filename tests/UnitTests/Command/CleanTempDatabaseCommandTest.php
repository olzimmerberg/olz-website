<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CleanTempDatabaseCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\CleanTempDatabaseCommand
 */
final class CleanTempDatabaseCommandTest extends UnitTestCase {
    public function testCleanTempDatabaseCommand(): void {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new CleanTempDatabaseCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\CleanTempDatabaseCommand...',
            'INFO Cleaning up 3 auth request entries...',
            'INFO Cleaning up 3 counter entries...',
            'INFO Successfully ran command Olz\Command\CleanTempDatabaseCommand.',
        ], $this->getLogs());
    }
}
