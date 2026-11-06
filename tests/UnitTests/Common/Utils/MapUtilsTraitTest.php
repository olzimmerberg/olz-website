<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\MapUtils;
use Olz\Common\Utils\MapUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class MapUtilsTraitConcreteUtils {
    use MapUtilsTrait;

    public function testOnlyMapUtils(): MapUtils {
        return $this->mapUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\MapUtilsTrait
 */
final class MapUtilsTraitTest extends UnitTestCase {
    public function testSetGetMapUtils(): void {
        $utils = new MapUtilsTraitConcreteUtils();
        $fake = $this->createMock(MapUtils::class);
        $utils->setMapUtils($fake);
        $this->assertSame($fake, $utils->testOnlyMapUtils());
    }
}
