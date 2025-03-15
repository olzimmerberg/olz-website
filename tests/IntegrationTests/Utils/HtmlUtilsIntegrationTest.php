<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\HtmlUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\HtmlUtils
 */
final class HtmlUtilsIntegrationTest extends IntegrationTestCase {
    public function testHtmlUtilsFromEnv(): void {
        self::bootKernel();
        $utils = self::getContainer()->get(HtmlUtils::class);

        $this->assertSame(HtmlUtils::class, get_class($utils));
    }
}
