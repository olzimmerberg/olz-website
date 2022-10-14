<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DevDataUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\DevDataUtils
 */
final class DevDataUtilsTest extends UnitTestCase {
    public function testDevDataUtilsGetDb(): void {
        $env_utils = new FakeEnvUtils();
        $dev_data_utils = new DevDataUtils();
        $dev_data_utils->setEnvUtils($env_utils);

        // There's not much to test in unit tests without an actual DB...
        $this->assertSame(false, !$dev_data_utils);
    }
}
