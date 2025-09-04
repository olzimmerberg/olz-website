<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HtmlUtils;
use Olz\Utils\HtmlUtilsTrait;

class HtmlUtilsTraitConcreteUtils {
    use HtmlUtilsTrait;

    public function testOnlyHtmlUtils(): HtmlUtils {
        return $this->htmlUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HtmlUtilsTrait
 */
final class HtmlUtilsTraitTest extends UnitTestCase {
    public function testSetGetHtmlUtils(): void {
        $utils = new HtmlUtilsTraitConcreteUtils();
        $fake = $this->createMock(HtmlUtils::class);
        $utils->setHtmlUtils($fake);
        $this->assertSame($fake, $utils->testOnlyHtmlUtils());
    }
}
