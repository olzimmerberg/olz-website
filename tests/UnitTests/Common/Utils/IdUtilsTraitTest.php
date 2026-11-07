<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\IdUtils;
use Olz\Common\Utils\IdUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class IdUtilsTraitConcreteUtils {
    use IdUtilsTrait;

    public function testOnlyIdUtils(): IdUtils {
        return $this->idUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\IdUtilsTrait
 */
final class IdUtilsTraitTest extends UnitTestCase {
    public function testSetGetIdUtils(): void {
        $utils = new IdUtilsTraitConcreteUtils();
        $fake = $this->createMock(IdUtils::class);
        $utils->setIdUtils($fake);
        $this->assertSame($fake, $utils->testOnlyIdUtils());
    }
}
