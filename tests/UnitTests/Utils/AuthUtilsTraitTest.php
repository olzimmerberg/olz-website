<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\AuthUtilsTrait;

class AuthUtilsTraitConcreteUtils {
    use AuthUtilsTrait;

    public function testOnlyAuthUtils(): AuthUtils {
        return $this->authUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtilsTrait
 */
final class AuthUtilsTraitTest extends UnitTestCase {
    public function testSetGetAuthUtils(): void {
        $utils = new AuthUtilsTraitConcreteUtils();
        $fake = $this->createMock(AuthUtils::class);
        $utils->setAuthUtils($fake);
        $this->assertSame($fake, $utils->testOnlyAuthUtils());
    }
}
