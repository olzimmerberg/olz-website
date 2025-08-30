<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DevDataUtils;
use Olz\Utils\DevDataUtilsTrait;

class DevDataUtilsTraitConcreteUtils {
    use DevDataUtilsTrait;

    public function testOnlyDevDataUtils(): DevDataUtils {
        return $this->devDataUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DevDataUtilsTrait
 */
final class DevDataUtilsTraitTest extends UnitTestCase {
    public function testSetGetDevDataUtils(): void {
        $utils = new DevDataUtilsTraitConcreteUtils();
        $fake = $this->createMock(DevDataUtils::class);
        $utils->setDevDataUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDevDataUtils());
    }
}
