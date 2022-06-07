<?php

declare(strict_types=1);

use Olz\Utils\StandardSession;

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @covers \StandardSession
 */
final class StandardSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(StandardSession::class));
    }
}
