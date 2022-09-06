<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AbstractSession;

/**
 * @internal
 *
 * @covers \Olz\Utils\AbstractSession
 */
final class AbstractSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(AbstractSession::class));
    }
}
