<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\DbUtils;
use Olz\Common\Utils\DbUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class DbUtilsTraitConcreteUtils {
    use DbUtilsTrait;

    public function testOnlyDbUtils(): DbUtils {
        return $this->dbUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\DbUtilsTrait
 */
final class DbUtilsTraitTest extends UnitTestCase {
    public function testSetGetDbUtils(): void {
        $utils = new DbUtilsTraitConcreteUtils();
        $fake = $this->createMock(DbUtils::class);
        $utils->setDbUtils($fake);
        $this->assertSame($fake, $utils->testOnlyDbUtils());
    }
}
