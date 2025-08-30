<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\ImageUtils;
use Olz\Utils\ImageUtilsTrait;

class ImageUtilsTraitConcreteUtils {
    use ImageUtilsTrait;

    public function testOnlyImageUtils(): ImageUtils {
        return $this->imageUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\ImageUtilsTrait
 */
final class ImageUtilsTraitTest extends UnitTestCase {
    public function testSetGetImageUtils(): void {
        $utils = new ImageUtilsTraitConcreteUtils();
        $fake = $this->createMock(ImageUtils::class);
        $utils->setImageUtils($fake);
        $this->assertSame($fake, $utils->testOnlyImageUtils());
    }
}
