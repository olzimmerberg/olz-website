<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\Session;
use Olz\Common\Utils\SessionTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class SessionTraitConcreteUtils {
    use SessionTrait;

    public function testOnlySession(): Session {
        return $this->session();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\SessionTrait
 */
final class SessionTraitTest extends UnitTestCase {
    public function testSetGetSession(): void {
        $utils = new SessionTraitConcreteUtils();
        $fake = $this->createMock(Session::class);
        $utils->setSession($fake);
        $this->assertSame($fake, $utils->testOnlySession());
    }
}
