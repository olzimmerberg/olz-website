<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpUtils;
use Olz\Utils\HttpUtilsTrait;

class HttpUtilsTraitConcreteUtils {
    use HttpUtilsTrait;

    public function testOnlyHttpUtils(): HttpUtils {
        return $this->httpUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HttpUtilsTrait
 */
final class HttpUtilsTraitTest extends UnitTestCase {
    public function testSetGetHttpUtils(): void {
        $utils = new HttpUtilsTraitConcreteUtils();
        $fake = $this->createMock(HttpUtils::class);
        $utils->setHttpUtils($fake);
        $this->assertSame($fake, $utils->testOnlyHttpUtils());
    }
}
