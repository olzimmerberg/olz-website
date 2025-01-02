<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\CheckTermineSolvIdCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\CheckTermineSolvIdCommand
 */
final class CheckTermineSolvIdCommandTest extends UnitTestCase {
    public function testCheckTermineSolvIdCommandYear(): void {
        $command = new CheckTermineSolvIdCommand();
        $input = new ArrayInput(['--year' => '2020']);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\CheckTermineSolvIdCommand...",
            "INFO Running with YEAR(start_date) = '2020'",
            "INFO DB: SELECT * FROM termine WHERE YEAR(start_date) = '2020' AND solv_uid IS NULL",
            "INFO Successfully ran command Olz\\Command\\CheckTermineSolvIdCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Command\CheckTermineSolvIdCommand...
            INFO: Running with YEAR(start_date) = '2020'
            Running with YEAR(start_date) = '2020'
            INFO: DB: SELECT * FROM termine WHERE YEAR(start_date) = '2020' AND solv_uid IS NULL
            INFO: Successfully ran command Olz\Command\CheckTermineSolvIdCommand.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testCheckTermineSolvIdCommandFuture(): void {
        $command = new CheckTermineSolvIdCommand();
        $input = new ArrayInput(['--future' => true]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\CheckTermineSolvIdCommand...",
            "INFO Running with start_date > '2020-03-13'",
            "INFO DB: SELECT * FROM termine WHERE start_date > '2020-03-13' AND solv_uid IS NULL",
            "INFO Successfully ran command Olz\\Command\\CheckTermineSolvIdCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Command\CheckTermineSolvIdCommand...
            INFO: Running with start_date > '2020-03-13'
            Running with start_date > '2020-03-13'
            INFO: DB: SELECT * FROM termine WHERE start_date > '2020-03-13' AND solv_uid IS NULL
            INFO: Successfully ran command Olz\Command\CheckTermineSolvIdCommand.

            ZZZZZZZZZZ, $output->fetch());
    }

    public function testCheckTermineSolvIdCommandRecent(): void {
        $command = new CheckTermineSolvIdCommand();
        $input = new ArrayInput(['--recent' => true]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\CheckTermineSolvIdCommand...",
            "INFO Running with last_modified_at > '2020-03-11 23:00:00'",
            "INFO DB: SELECT * FROM termine WHERE last_modified_at > '2020-03-11 23:00:00' AND solv_uid IS NULL",
            "INFO Successfully ran command Olz\\Command\\CheckTermineSolvIdCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: Running command Olz\Command\CheckTermineSolvIdCommand...
            INFO: Running with last_modified_at > '2020-03-11 23:00:00'
            Running with last_modified_at > '2020-03-11 23:00:00'
            INFO: DB: SELECT * FROM termine WHERE last_modified_at > '2020-03-11 23:00:00' AND solv_uid IS NULL
            INFO: Successfully ran command Olz\Command\CheckTermineSolvIdCommand.

            ZZZZZZZZZZ, $output->fetch());
    }
}
