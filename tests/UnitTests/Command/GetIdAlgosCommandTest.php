<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\GetIdAlgosCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\GetIdAlgosCommand
 */
final class GetIdAlgosCommandTest extends UnitTestCase {
    public function testGetIdAlgosCommandSuccess(): void {
        $command = new GetIdAlgosCommand();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $return_code = $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\GetIdAlgosCommand...",
            "INFO Successfully ran command Olz\\Command\\GetIdAlgosCommand.",
        ], $this->getLogs());
        $this->assertSame(Command::SUCCESS, $return_code);
        $this->assertMatchesRegularExpression(
            "/\"aes-128-cbc\"/i",
            $output->fetch(),
        );
    }
}
