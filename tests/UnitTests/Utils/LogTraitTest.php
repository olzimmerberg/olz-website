<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\LogTrait;
use Psr\Log\LoggerInterface;

class LoggerInterfaceTraitConcreteUtils {
    use LogTrait;

    public function testOnlyLog(): LoggerInterface {
        return $this->log();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\LogTrait
 */
final class LogTraitTest extends UnitTestCase {
    public function testSetGetLoggerInterface(): void {
        $utils = new LoggerInterfaceTraitConcreteUtils();
        $fake = $this->createMock(LoggerInterface::class);
        $utils->setLog($fake);
        $this->assertSame($fake, $utils->testOnlyLog());
    }
}
