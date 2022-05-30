<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/utils/client/HtmlUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \HtmlUtils
 */
final class HtmlUtilsIntegrationTest extends IntegrationTestCase {
    public function testHtmlUtilsFromEnv(): void {
        $html_utils = HtmlUtils::fromEnv();

        $this->assertSame(false, !$html_utils);
    }
}
