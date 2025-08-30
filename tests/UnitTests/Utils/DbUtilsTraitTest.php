<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\DbUtilsTrait;

class DbUtilsTraitConcreteUtils {
    use DbUtilsTrait;

    public function testOnlyDbUtils(): DbUtils {
        return $this->dbUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\DbUtilsTrait
 */
final class DbUtilsTraitTest extends UnitTestCase {
    public function testSetGetDbUtils(): void {
        $utils = new DbUtilsTraitConcreteUtils();
        $fake = $this->createMock(DbUtils::class);
        $utils->setDbUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDbUtils());
    }
}
