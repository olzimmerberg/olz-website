<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\UploadUtils;
use Olz\Common\Utils\UploadUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class UploadUtilsTraitConcreteUtils {
    use UploadUtilsTrait;

    public function testOnlyUploadUtils(): UploadUtils {
        return $this->uploadUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\UploadUtilsTrait
 */
final class UploadUtilsTraitTest extends UnitTestCase {
    public function testSetGetUploadUtils(): void {
        $utils = new UploadUtilsTraitConcreteUtils();
        $fake = $this->createMock(UploadUtils::class);
        $utils->setUploadUtils($fake);
        $this->assertSame($fake, $utils->testOnlyUploadUtils());
    }
}
