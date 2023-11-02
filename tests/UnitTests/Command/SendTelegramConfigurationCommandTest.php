<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendTelegramConfigurationCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\SendTelegramConfigurationCommand
 */
final class SendTelegramConfigurationCommandTest extends UnitTestCase {
    public function testSendTelegramConfigurationCommandSuccess(): void {
        $command = new SendTelegramConfigurationCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\SendTelegramConfigurationCommand...",
            "INFO Successfully ran command Olz\\Command\\SendTelegramConfigurationCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame(true, WithUtilsCache::get('telegramUtils')->configurationSent);
    }
}
