<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\EnvUtils;
use Olz\Utils\EnvUtilsTrait;

class EnvUtilsTraitConcreteUtils {
    use EnvUtilsTrait;

    public function testOnlyEnvUtils(): EnvUtils {
        return $this->envUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EnvUtilsTrait
 */
final class EnvUtilsTraitTest extends UnitTestCase {
    public function testSetGetEnvUtils(): void {
        $utils = new EnvUtilsTraitConcreteUtils();
        $fake = $this->createMock(EnvUtils::class);
        $utils->setEnvUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEnvUtils());
    }
}
