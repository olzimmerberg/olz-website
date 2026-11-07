<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\HtmlUtils;
use Olz\Common\Utils\HtmlUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class HtmlUtilsTraitConcreteUtils {
    use HtmlUtilsTrait;

    public function testOnlyHtmlUtils(): HtmlUtils {
        return $this->htmlUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\HtmlUtilsTrait
 */
final class HtmlUtilsTraitTest extends UnitTestCase {
    public function testSetGetHtmlUtils(): void {
        $utils = new HtmlUtilsTraitConcreteUtils();
        $fake = $this->createMock(HtmlUtils::class);
        $utils->setHtmlUtils($fake);
        $this->assertSame($fake, $utils->testOnlyHtmlUtils());
    }
}
