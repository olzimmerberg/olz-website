<?php

declare(strict_types=1);

use Olz\Utils\AbstractSession;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\AbstractSession
 */
final class AbstractSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(AbstractSession::class));
    }
}
