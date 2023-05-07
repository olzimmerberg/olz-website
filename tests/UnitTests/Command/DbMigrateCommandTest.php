<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbMigrateCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\DbMigrateCommand
 */
final class DbMigrateCommandTest extends UnitTestCase {
    public function testDbMigrateCommandSuccess(): void {
        $command = new DbMigrateCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbMigrateCommand...",
            "INFO Successfully ran command Olz\\Command\\DbMigrateCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame([
            ['migrateTo', 'latest'],
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }
}
