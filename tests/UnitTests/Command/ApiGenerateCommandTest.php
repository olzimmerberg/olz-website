<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Api\OlzApi;
use Olz\Command\ApiGenerateCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\ApiGenerateCommand
 */
final class ApiGenerateCommandTest extends UnitTestCase {
    public function testApiGenerateCommandSuccess(): void {
        $fake_olz_api = $this->createMock(OlzApi::class);
        $fake_olz_api->expects($this->once())->method('generate');
        $command = new ApiGenerateCommand($fake_olz_api);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\ApiGenerateCommand...",
            "INFO Successfully ran command Olz\\Command\\ApiGenerateCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Running command Olz\Command\ApiGenerateCommand...
            Successfully ran command Olz\Command\ApiGenerateCommand.

            ZZZZZZZZZZ, $output->fetch());
    }
}
