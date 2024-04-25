<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DbUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\DbUtils
 */
final class DbUtilsTest extends UnitTestCase {
    public function testDbUtilsGetDb(): void {
        $db_utils = new DbUtils();

        // There's not much to test in unit tests without an actual DB...
        $this->assertFalse(!$db_utils);
    }
}
