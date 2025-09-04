<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MapUtils;
use Olz\Utils\MapUtilsTrait;

class MapUtilsTraitConcreteUtils {
    use MapUtilsTrait;

    public function testOnlyMapUtils(): MapUtils {
        return $this->mapUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\MapUtilsTrait
 */
final class MapUtilsTraitTest extends UnitTestCase {
    public function testSetGetMapUtils(): void {
        $utils = new MapUtilsTraitConcreteUtils();
        $fake = $this->createMock(MapUtils::class);
        $utils->setMapUtils($fake);
        $this->assertSame($fake, $utils->testOnlyMapUtils());
    }
}
