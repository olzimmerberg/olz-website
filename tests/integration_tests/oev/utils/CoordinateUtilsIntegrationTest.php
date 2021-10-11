<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/oev/utils/CoordinateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \CoordinateUtils
 */
final class CoordinateUtilsIntegrationTest extends IntegrationTestCase {
    public function testCoordinateUtilsFromEnv(): void {
        $coordinate_utils = CoordinateUtils::fromEnv();

        $this->assertSame(false, !$coordinate_utils);
    }
}
