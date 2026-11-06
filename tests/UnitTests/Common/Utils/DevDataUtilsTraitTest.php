<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\DevDataUtils;
use Olz\Common\Utils\DevDataUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class DevDataUtilsTraitConcreteUtils {
    use DevDataUtilsTrait;

    public function testOnlyDevDataUtils(): DevDataUtils {
        return $this->devDataUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\DevDataUtilsTrait
 */
final class DevDataUtilsTraitTest extends UnitTestCase {
    public function testSetGetDevDataUtils(): void {
        $utils = new DevDataUtilsTraitConcreteUtils();
        $fake = $this->createMock(DevDataUtils::class);
        $utils->setDevDataUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDevDataUtils());
    }
}
