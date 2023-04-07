<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Common;

use Olz\Command\Common\OlzCommand;
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
class OlzCommandForTest extends OlzCommand {
    public $returnCode;
    public $failWithError;

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $output->writeln('Test handle');
        $this->log()->info('Test handle');
        if ($this->failWithError) {
            throw $this->failWithError;
        }
        return $this->returnCode;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Common\OlzCommand
 */
final class OlzCommandTest extends UnitTestCase {
    public function testOlzCommandSuccessCode(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OlzCommandForTest();
        $command->returnCode = Command::SUCCESS;
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame("Test handle\n", $output->fetch());
    }

    public function testOlzCommandFailureCode(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OlzCommandForTest();
        $command->returnCode = Command::FAILURE;
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "ERROR Failed running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertSame("Test handle\n", $output->fetch());
    }

    public function testOlzCommandUnknownCode(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OlzCommandForTest();
        $command->returnCode = 90684597;
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "ERROR Command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest finished with unknown status 90684597.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(90684597, $return_code);
        $this->assertSame("Test handle\n", $output->fetch());
    }

    public function testOlzCommandError(): void {
        $logger = Fake\FakeLogger::create();
        $command = new OlzCommandForTest();
        $command->failWithError = new \Exception('test error');
        $command->setLog($logger);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "ERROR Error running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest: test error.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertSame("Test handle\n", $output->fetch());
    }
}