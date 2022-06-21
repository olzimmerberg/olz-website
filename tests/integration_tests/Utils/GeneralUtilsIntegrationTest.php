<?php

declare(strict_types=1);

use Olz\Utils\GeneralUtils;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\GeneralUtils
 */
final class GeneralUtilsIntegrationTest extends IntegrationTestCase {
    public function testGeneralUtilsFromEnv(): void {
        $general_utils = GeneralUtils::fromEnv();

        $this->assertSame(false, !$general_utils);
    }
}
