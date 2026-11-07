<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\EntityUtils;
use Olz\Common\Utils\EntityUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class EntityUtilsTraitConcreteUtils {
    use EntityUtilsTrait;

    public function testOnlyEntityUtils(): EntityUtils {
        return $this->entityUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\EntityUtilsTrait
 */
final class EntityUtilsTraitTest extends UnitTestCase {
    public function testSetGetEntityUtils(): void {
        $utils = new EntityUtilsTraitConcreteUtils();
        $fake = $this->createMock(EntityUtils::class);
        $utils->setEntityUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEntityUtils());
    }
}
