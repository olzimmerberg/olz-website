<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\DevDataUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\DevDataUtils
 */
final class DevDataUtilsTest extends UnitTestCase {
    public function testDevDataUtils(): void {
        $dev_data_utils = new DevDataUtils();

        // There's not much to test in unit tests without an actual DB...
        $this->assertEquals(new DevDataUtils(), $dev_data_utils);
    }
}
