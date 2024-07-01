<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\DbResetCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
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
        WithUtilsCache::get('envUtils')->app_env = 'prod';
        $command = new DbResetCommand();
        $input = new ArrayInput(['mode' => 'content']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);
        try {
            $this->assertSame([
                "NOTICE Command Olz\\Command\\DbResetCommand not allowed in app env prod.",
            ], $this->getLogs());
            $this->assertSame(Command::INVALID, $return_code);
            $this->assertSame(<<<'ZZZZZZZZZZ'
                Fake.NOTICE: Command Olz\Command\DbResetCommand not allowed in app env prod. [] []
                Command Olz\Command\DbResetCommand not allowed in app env prod.

                ZZZZZZZZZZ, $output->fetch());
            $this->assertSame([], WithUtilsCache::get('devDataUtils')->commands_called);
            $_ENV = $env_backup;
        } catch (\Throwable $th) {
            $_ENV = $env_backup;
            throw $th;
        }
    }

    public function testDbResetCommandModeContent(): void {
        $command = new DbResetCommand();
        $input = new ArrayInput(['mode' => 'content']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Fake.INFO: Running command Olz\Command\DbResetCommand... [] []
            Database content reset successful.
            Fake.INFO: Successfully ran command Olz\Command\DbResetCommand. [] []

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            'resetDbContent',
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }

    public function testDbResetCommandModeStructure(): void {
        $command = new DbResetCommand();
        $input = new ArrayInput(['mode' => 'structure']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Fake.INFO: Running command Olz\Command\DbResetCommand... [] []
            Database structure reset successful.
            Fake.INFO: Successfully ran command Olz\Command\DbResetCommand. [] []

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            'resetDbStructure',
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }

    public function testDbResetCommandModeFull(): void {
        $command = new DbResetCommand();
        $input = new ArrayInput(['mode' => 'full']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "INFO Successfully ran command Olz\\Command\\DbResetCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Fake.INFO: Running command Olz\Command\DbResetCommand... [] []
            Database full reset successful.
            Fake.INFO: Successfully ran command Olz\Command\DbResetCommand. [] []

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([
            'fullResetDb',
        ], WithUtilsCache::get('devDataUtils')->commands_called);
    }

    public function testDbResetCommandInvalidMode(): void {
        $command = new DbResetCommand();
        $input = new ArrayInput(['mode' => 'invalid']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\DbResetCommand...",
            "NOTICE Command Olz\\Command\\DbResetCommand called with invalid arguments.",
        ], $this->getLogs());
        $this->assertSame(Command::INVALID, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Fake.INFO: Running command Olz\Command\DbResetCommand... [] []
            Invalid mode: invalid.
            Fake.NOTICE: Command Olz\Command\DbResetCommand called with invalid arguments. [] []

            ZZZZZZZZZZ, $output->fetch());
        $this->assertSame([], WithUtilsCache::get('devDataUtils')->commands_called);
    }
}
