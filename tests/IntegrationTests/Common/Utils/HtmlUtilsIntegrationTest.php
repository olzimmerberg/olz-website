<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Common\Utils;

use Olz\Common\Utils\HtmlUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\HtmlUtils
 */
final class HtmlUtilsIntegrationTest extends IntegrationTestCase {
    public function testHtmlUtilsFromEnv(): void {
        self::bootKernel();
        $utils = self::getContainer()->get(HtmlUtils::class);

        $this->assertSame(HtmlUtils::class, get_class($utils));
    }
}
