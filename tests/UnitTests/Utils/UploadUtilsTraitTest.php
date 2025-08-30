<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\UploadUtils;
use Olz\Utils\UploadUtilsTrait;

class UploadUtilsTraitConcreteUtils {
    use UploadUtilsTrait;

    public function testOnlyUploadUtils(): UploadUtils {
        return $this->uploadUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\UploadUtilsTrait
 */
final class UploadUtilsTraitTest extends UnitTestCase {
    public function testSetGetUploadUtils(): void {
        $utils = new UploadUtilsTraitConcreteUtils();
        $fake = $this->createMock(UploadUtils::class);
        $utils->setUploadUtils($fake);
        $this->assertSame($fake, $utils->testOnlyUploadUtils());
    }
}
