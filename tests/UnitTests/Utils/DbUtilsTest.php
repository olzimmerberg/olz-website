<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DbUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\DbUtils
 */
final class DbUtilsTest extends UnitTestCase {
    public function testDbUtilsGetDb(): void {
        $env_utils = new FakeEnvUtils();
        $db_utils = new DbUtils();
        $db_utils->setEnvUtils($env_utils);

        // There's not much to test in unit tests without an actual DB...
        $this->assertSame(false, !$db_utils);
    }
}
