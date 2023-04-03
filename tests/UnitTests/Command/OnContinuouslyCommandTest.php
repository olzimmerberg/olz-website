<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\OnContinuouslyCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\OnContinuouslyCommand
 */
final class OnContinuouslyCommandTest extends UnitTestCase {
    public function testDummy(): void {
        $command = new OnContinuouslyCommand();
        $this->assertSame(true, (bool) $command);
    }
}
