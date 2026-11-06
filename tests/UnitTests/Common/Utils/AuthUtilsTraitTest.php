<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\AuthUtils;
use Olz\Common\Utils\AuthUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class AuthUtilsTraitConcreteUtils {
    use AuthUtilsTrait;

    public function testOnlyAuthUtils(): AuthUtils {
        return $this->authUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\AuthUtilsTrait
 */
final class AuthUtilsTraitTest extends UnitTestCase {
    public function testSetGetAuthUtils(): void {
        $utils = new AuthUtilsTraitConcreteUtils();
        $fake = $this->createMock(AuthUtils::class);
        $utils->setAuthUtils($fake);
        $this->assertSame($fake, $utils->testOnlyAuthUtils());
    }
}
