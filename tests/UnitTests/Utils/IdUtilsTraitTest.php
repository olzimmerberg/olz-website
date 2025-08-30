<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\IdUtils;
use Olz\Utils\IdUtilsTrait;

class IdUtilsTraitConcreteUtils {
    use IdUtilsTrait;

    public function testOnlyIdUtils(): IdUtils {
        return $this->idUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\IdUtilsTrait
 */
final class IdUtilsTraitTest extends UnitTestCase {
    public function testSetGetIdUtils(): void {
        $utils = new IdUtilsTraitConcreteUtils();
        $fake = $this->createMock(IdUtils::class);
        $utils->setIdUtils($fake);
        $this->assertSame($fake, $utils->testOnlyIdUtils());
    }
}
