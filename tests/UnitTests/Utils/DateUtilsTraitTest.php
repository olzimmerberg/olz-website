<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\DateUtilsTrait;

class DateUtilsTraitConcreteUtils {
    use DateUtilsTrait;

    public function testOnlyDateUtils(): DateUtils {
        return $this->dateUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DateUtilsTrait
 */
final class DateUtilsTraitTest extends UnitTestCase {
    public function testSetGetDateUtils(): void {
        $utils = new DateUtilsTraitConcreteUtils();
        $fake = $this->createMock(DateUtils::class);
        $utils->setDateUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDateUtils());
    }
}
