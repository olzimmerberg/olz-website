<?php

declare(strict_types=1);

use Olz\Apps\Oev\Utils\CoordinateUtils;

require_once __DIR__.'/../../../common/IntegrationTestCase.php';

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
