<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\StandardSession;

/**
 * @internal
 * @covers \Olz\Utils\StandardSession
 */
final class StandardSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(StandardSession::class));
    }
}
