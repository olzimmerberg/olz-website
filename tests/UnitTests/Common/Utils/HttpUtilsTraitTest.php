<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\HttpUtils;
use Olz\Common\Utils\HttpUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class HttpUtilsTraitConcreteUtils {
    use HttpUtilsTrait;

    public function testOnlyHttpUtils(): HttpUtils {
        return $this->httpUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\HttpUtilsTrait
 */
final class HttpUtilsTraitTest extends UnitTestCase {
    public function testSetGetHttpUtils(): void {
        $utils = new HttpUtilsTraitConcreteUtils();
        $fake = $this->createMock(HttpUtils::class);
        $utils->setHttpUtils($fake);
        $this->assertSame($fake, $utils->testOnlyHttpUtils());
    }
}
