<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Utils;

use Olz\News\Utils\NewsUtils;
use Olz\News\Utils\NewsUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class NewsUtilsTraitConcreteUtils {
    use NewsUtilsTrait;

    public function testOnlyNewsUtils(): NewsUtils {
        return $this->NewsUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\News\Utils\NewsUtilsTrait
 */
final class NewsUtilsTraitTest extends UnitTestCase {
    public function testSetGetNewsUtils(): void {
        $utils = new NewsUtilsTraitConcreteUtils();
        $fake = $this->createMock(NewsUtils::class);
        $utils->setNewsUtils($fake);
        $this->assertSame($fake, $utils->testOnlyNewsUtils());
    }
}
