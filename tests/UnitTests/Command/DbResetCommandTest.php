<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbResetCommand;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\DbResetCommand
 */
final class DbResetCommandTest extends UnitTestCase {
    public function testDbResetCommandOnProd(): void {
        $env_backup = $_ENV;
        $_ENV = [...$_ENV, 'APP_ENV' => 'prod'];
        $dev_data_utils = new Fake\FakeDevDataUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $env_utils->app_env = 'prod';
        $logger = Fake\FakeLogger::create();
        $command = new DbResetCommand();
        $command->setDevDataUtils($dev_data_utils);
        $command->setEnvUtils($env_utils);
        $command->setLog($logger);
        $input = new ArrayInput(['mode' => 'content']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "NOTICE Command Olz\\Command\\DbResetCommand not allowed in app env prod.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::INVALID, $return_code);
        $this->assertSame(
            "Command Olz\\Command\\DbResetCommand not allowed in app env prod.\n",
            $output->fetch(),
        );
        $this->assertSame([], $dev_data_utils->commands_called);

        $_ENV = $env_backup;
    }

    public function testDbResetCommandModeContent(): void {
        $dev_data_utils = new Fake\FakeDevDataUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $command = new DbResetCommand();
        $command->setDevDataUtils($dev_data_utils);
        $command->setEnvUtils($env_utils);
        $command->setLog($logger);
        $input = new ArrayInput(['mode' => 'content']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(
            "Database content reset successful.\n",
            $output->fetch(),
        );
        $this->assertSame([
            'resetDbContent',
        ], $dev_data_utils->commands_called);
    }

    public function testDbResetCommandModeStructure(): void {
        $dev_data_utils = new Fake\FakeDevDataUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $command = new DbResetCommand();
        $command->setDevDataUtils($dev_data_utils);
        $command->setEnvUtils($env_utils);
        $command->setLog($logger);
        $input = new ArrayInput(['mode' => 'structure']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(
            "Database structure reset successful.\n",
            $output->fetch(),
        );
        $this->assertSame([
            'resetDbStructure',
        ], $dev_data_utils->commands_called);
    }

    public function testDbResetCommandModeFull(): void {
        $dev_data_utils = new Fake\FakeDevDataUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $command = new DbResetCommand();
        $command->setDevDataUtils($dev_data_utils);
        $command->setEnvUtils($env_utils);
        $command->setLog($logger);
        $input = new ArrayInput(['mode' => 'full']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(
            "Database full reset successful.\n",
            $output->fetch(),
        );
        $this->assertSame([
            'fullResetDb',
        ], $dev_data_utils->commands_called);
    }

    public function testDbResetCommandInvalidMode(): void {
        $dev_data_utils = new Fake\FakeDevDataUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $command = new DbResetCommand();
        $command->setDevDataUtils($dev_data_utils);
        $command->setEnvUtils($env_utils);
        $command->setLog($logger);
        $input = new ArrayInput(['mode' => 'invalid']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "NOTICE Command Olz\\Command\\DbResetCommand called with invalid arguments.",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(Command::INVALID, $return_code);
        $this->assertSame(
            "Invalid mode: invalid.\n",
            $output->fetch(),
        );
        $this->assertSame([], $dev_data_utils->commands_called);
    }
}
