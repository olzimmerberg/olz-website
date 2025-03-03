<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\LiveDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\LiveDateUtils
 */
final class LiveDateUtilsTest extends UnitTestCase {
    public function testExists(): void {
        $utils = new LiveDateUtils();
        $this->assertSame(date('Y-m-d'), $utils->getCurrentDateInFormat('Y-m-d'));
    }
}
