<?php

declare(strict_types=1);

use Olz\Utils\HtmlUtils;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\HtmlUtils
 */
final class HtmlUtilsIntegrationTest extends IntegrationTestCase {
    public function testHtmlUtilsFromEnv(): void {
        $html_utils = HtmlUtils::fromEnv();

        $this->assertSame(false, !$html_utils);
    }
}
