<?php

declare(strict_types=1);

require_once __DIR__.'/../../../src/utils/FieldUtils.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \FieldUtils
 */
final class FieldUtilsIntegrationTest extends IntegrationTestCase {
    public function testFieldUtilsFromEnv(): void {
        $general_utils = FieldUtils::fromEnv();

        $this->assertSame(false, !$general_utils);
    }
}
