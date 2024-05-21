<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbDiffCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\DbDiffCommand
 */
final class DbDiffCommandTest extends UnitTestCase {
    public function testDbDiffCommandSuccess(): void {
        $command = new DbDiffCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbDiffCommand...",
            "INFO Successfully ran command Olz\\Command\\DbDiffCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame('fake output', $output->fetch());
        $this->assertSame([
            'generateMigration',
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }
}
