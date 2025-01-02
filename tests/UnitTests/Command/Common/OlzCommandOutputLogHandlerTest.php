<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Common;

use Monolog\Level;
use Monolog\LogRecord;
use Olz\Command\Common\OlzCommandOutputLogHandler;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\Common\OlzCommandOutputLogHandler
 */
final class OlzCommandOutputLogHandlerTest extends UnitTestCase {
    public function testOlzCommandOutputLogHandler(): void {
        $output = new BufferedOutput();
        $handler = new OlzCommandOutputLogHandler($output);

        $handler->handle(new LogRecord(
            new \DateTimeImmutable('2020-03-13'),
            'channel',
            Level::Info,
            "message",
        ));

        $this->assertSame(<<<'ZZZZZZZZZZ'
            INFO: message

            ZZZZZZZZZZ, $output->fetch());
    }
}
