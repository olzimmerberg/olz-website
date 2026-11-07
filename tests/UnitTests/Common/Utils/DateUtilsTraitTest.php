<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\DateUtils;
use Olz\Common\Utils\DateUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class DateUtilsTraitConcreteUtils {
    use DateUtilsTrait;

    public function testOnlyDateUtils(): DateUtils {
        return $this->dateUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\DateUtilsTrait
 */
final class DateUtilsTraitTest extends UnitTestCase {
    public function testSetGetDateUtils(): void {
        $utils = new DateUtilsTraitConcreteUtils();
        $fake = $this->createMock(DateUtils::class);
        $utils->setDateUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDateUtils());
    }
}
