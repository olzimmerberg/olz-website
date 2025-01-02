<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Common;

use Olz\Command\Common\OlzCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
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
    /** @var array<string> */
    public array $allowedAppEnvs = ['test'];
    public int $returnCode = 0;
    public ?\Exception $failWithError = null;

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return $this->allowedAppEnvs;
    }

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
    public function testOlzCommandDisallowedAppEnv(): void {
        $command = new OlzCommandForTest();
        $command->allowedAppEnvs = [];
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "NOTICE Command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest not allowed in app env test.",
        ], $this->getLogs());
        $this->assertSame(Command::INVALID, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            NOTICE: Command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest not allowed in app env test.
            Command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest not allowed in app env test.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandInconsistentAppEnv(): void {
        WithUtilsCache::get('envUtils')->app_env = 'not_test';
        $command = new OlzCommandForTest();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "ERROR Error running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest: OLZ and symfony app env do not match (not_test vs. test).",
        ], $this->getLogs());
        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            ERROR: Error running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest: OLZ and symfony app env do not match (not_test vs. test).
            Error running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest: OLZ and symfony app env do not match (not_test vs. test).

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandSuccessCode(): void {
        $command = new OlzCommandForTest();
        $command->returnCode = Command::SUCCESS;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest...
            Test handle
            INFO: Test handle
            INFO: Successfully ran command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandFailureCode(): void {
        $command = new OlzCommandForTest();
        $command->returnCode = Command::FAILURE;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "NOTICE Failed running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest.",
        ], $this->getLogs());
        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest...
            Test handle
            INFO: Test handle
            NOTICE: Failed running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandInvalidCode(): void {
        $command = new OlzCommandForTest();
        $command->returnCode = Command::INVALID;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "NOTICE Command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest called with invalid arguments.",
        ], $this->getLogs());
        $this->assertSame(Command::INVALID, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest...
            Test handle
            INFO: Test handle
            NOTICE: Command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest called with invalid arguments.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandUnknownCode(): void {
        $command = new OlzCommandForTest();
        $command->returnCode = 90684597;
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "WARNING Command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest finished with unknown status 90684597.",
        ], $this->getLogs());
        $this->assertSame(90684597, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest...
            Test handle
            INFO: Test handle
            WARNING: Command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest finished with unknown status 90684597.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testOlzCommandError(): void {
        $command = new OlzCommandForTest();
        $command->failWithError = new \Exception('test error');
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest...",
            "INFO Test handle",
            "ERROR Error running command Olz\\Tests\\UnitTests\\Command\\Common\\OlzCommandForTest: test error.",
        ], $this->getLogs());
        $this->assertSame(Command::FAILURE, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest...
            Test handle
            INFO: Test handle
            ERROR: Error running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest: test error.
            Error running command Olz\Tests\UnitTests\Command\Common\OlzCommandForTest: test error.

            ZZZZZZZZZZ, $output->fetch());
    }
}
