<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\ImageUtils;
use Olz\Common\Utils\ImageUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class ImageUtilsTraitConcreteUtils {
    use ImageUtilsTrait;

    public function testOnlyImageUtils(): ImageUtils {
        return $this->imageUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\ImageUtilsTrait
 */
final class ImageUtilsTraitTest extends UnitTestCase {
    public function testSetGetImageUtils(): void {
        $utils = new ImageUtilsTraitConcreteUtils();
        $fake = $this->createMock(ImageUtils::class);
        $utils->setImageUtils($fake);
        $this->assertSame($fake, $utils->testOnlyImageUtils());
    }
}
