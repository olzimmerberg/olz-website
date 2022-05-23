<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../public/_/utils/session/AbstractSession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \AbstractSession
 */
final class AbstractSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(AbstractSession::class));
    }
}
