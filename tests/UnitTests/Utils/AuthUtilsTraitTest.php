<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\AuthUtilsTrait;

class AuthUtilsTraitConcreteClass {
    use AuthUtilsTrait;

    public function testOnlyGetAuthUtils(): AuthUtils {
        return $this->authUtils;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtilsTrait
 */
final class AuthUtilsTraitTest extends UnitTestCase {
    public function testAuthUtilsTrait(): void {
        $auth_utils = new FakeAuthUtils();
        $concrete = new AuthUtilsTraitConcreteClass();
        $concrete->setAuthUtils($auth_utils);
        $this->assertSame($auth_utils, $concrete->testOnlyGetAuthUtils());
    }
}
