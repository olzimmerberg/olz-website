<?php

declare(strict_types=1);

require_once __DIR__.'/../../../public/_/utils/GeneralUtils.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \GeneralUtils
 */
final class GeneralUtilsIntegrationTest extends IntegrationTestCase {
    public function testGeneralUtilsFromEnv(): void {
        $general_utils = GeneralUtils::fromEnv();

        $this->assertSame(false, !$general_utils);
    }
}
