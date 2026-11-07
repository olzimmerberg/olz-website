<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\EnvUtils;
use Olz\Common\Utils\EnvUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class EnvUtilsTraitConcreteUtils {
    use EnvUtilsTrait;

    public function testOnlyEnvUtils(): EnvUtils {
        return $this->envUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\EnvUtilsTrait
 */
final class EnvUtilsTraitTest extends UnitTestCase {
    public function testSetGetEnvUtils(): void {
        $utils = new EnvUtilsTraitConcreteUtils();
        $fake = $this->createMock(EnvUtils::class);
        $utils->setEnvUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEnvUtils());
    }
}
