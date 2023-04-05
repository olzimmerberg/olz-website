<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class OnContinuouslyCommandForTest extends OnContinuouslyCommand {
    public $commandsCalled = [];

    public function callCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $this->commandsCalled[] = $command_name;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\OnContinuouslyCommand
 */
final class OnContinuouslyCommandTest extends UnitTestCase {
    public function testOnContinuouslyCommandSuccess(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OnContinuouslyCommandForTest();
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\OnContinuouslyCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("", $output->fetch());
        $this->assertSame([
            'olz:test',
            'olz:processEmail',
        ], $command->commandsCalled);
    }
}
