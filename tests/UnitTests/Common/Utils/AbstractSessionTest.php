<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\AbstractSession;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\AbstractSession
 */
final class AbstractSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(AbstractSession::class));
    }
}
