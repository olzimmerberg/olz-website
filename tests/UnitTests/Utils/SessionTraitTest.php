<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\Session;
use Olz\Utils\SessionTrait;

class SessionTraitConcreteUtils {
    use SessionTrait;

    public function testOnlySession(): Session {
        return $this->session();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\SessionTrait
 */
final class SessionTraitTest extends UnitTestCase {
    public function testSetGetSession(): void {
        $utils = new SessionTraitConcreteUtils();
        $fake = $this->createMock(Session::class);
        $utils->setSession($fake);
        $this->assertSame($fake, $utils->testOnlySession());
    }
}
