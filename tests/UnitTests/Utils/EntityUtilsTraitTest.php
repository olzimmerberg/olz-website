<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\EntityUtils;
use Olz\Utils\EntityUtilsTrait;

class EntityUtilsTraitConcreteUtils {
    use EntityUtilsTrait;

    public function testOnlyEntityUtils(): EntityUtils {
        return $this->entityUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EntityUtilsTrait
 */
final class EntityUtilsTraitTest extends UnitTestCase {
    public function testSetGetEntityUtils(): void {
        $utils = new EntityUtilsTraitConcreteUtils();
        $fake = $this->createMock(EntityUtils::class);
        $utils->setEntityUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEntityUtils());
    }
}
