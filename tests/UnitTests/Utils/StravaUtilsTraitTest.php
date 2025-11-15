<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\StravaUtils;
use Olz\Utils\StravaUtilsTrait;

class StravaUtilsTraitConcreteUtils {
    use StravaUtilsTrait;

    public function testOnlyStravaUtils(): StravaUtils {
        return $this->stravaUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\StravaUtilsTrait
 */
final class StravaUtilsTraitTest extends UnitTestCase {
    public function testSetGetStravaUtils(): void {
        $utils = new StravaUtilsTraitConcreteUtils();
        $fake = $this->createMock(StravaUtils::class);
        $utils->setStravaUtils($fake);
        $this->assertSame($fake, $utils->testOnlyStravaUtils());
    }
}
