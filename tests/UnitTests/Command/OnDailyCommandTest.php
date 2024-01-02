<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnDailyCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\OnDailyCommand
 */
final class OnDailyCommandTest extends UnitTestCase {
    public function testOnDailyCommandSuccess(): void {
        $command = new OnDailyCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\OnDailyCommand...",
            "INFO Successfully ran command Olz\\Command\\OnDailyCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame([
            'olz:clean-temp-directory ',
            'olz:send-telegram-configuration ',
            'olz:sync-solv ',
            'olz:send-test-email ',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
