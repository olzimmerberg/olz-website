<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\UpdateMeasurementsCommand;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\UpdateMeasurementsCommand
 */
final class UpdateMeasurementsCommandTest extends UnitTestCase {
    public function testUpdateMeasurementsCommand(): void {
        $command = new UpdateMeasurementsCommand();
        $this->assertSame(UpdateMeasurementsCommand::class, get_class($command));
    }
}
