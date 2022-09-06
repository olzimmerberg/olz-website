<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\CoordinateUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Oev\Utils\CoordinateUtils
 */
final class CoordinateUtilsIntegrationTest extends IntegrationTestCase {
    public function testCoordinateUtilsFromEnv(): void {
        $coordinate_utils = CoordinateUtils::fromEnv();

        $this->assertSame(false, !$coordinate_utils);
    }
}
