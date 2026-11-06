<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\StravaUtils;
use Olz\Common\Utils\StravaUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class StravaUtilsTraitConcreteUtils {
    use StravaUtilsTrait;

    public function testOnlyStravaUtils(): StravaUtils {
        return $this->stravaUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\StravaUtilsTrait
 */
final class StravaUtilsTraitTest extends UnitTestCase {
    public function testSetGetStravaUtils(): void {
        $utils = new StravaUtilsTraitConcreteUtils();
        $fake = $this->createMock(StravaUtils::class);
        $utils->setStravaUtils($fake);
        $this->assertSame($fake, $utils->testOnlyStravaUtils());
    }
}
