<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Oev\Utils;

use Olz\Apps\Oev\Utils\CoordinateUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 * @covers \Olz\Apps\Oev\Utils\CoordinateUtils
 */
final class CoordinateUtilsTest extends UnitTestCase {
    public function testGetCenter(): void {
        $coordinate_utils = new CoordinateUtils();
        $this->assertSame(
            ['x' => 1, 'y' => 2],
            $coordinate_utils->getCenter([
                ['x' => 0, 'y' => 1],
                ['x' => 2, 'y' => 3],
                ['x' => 1, 'y' => 2],
            ]),
        );
    }

    public function testGetDistance(): void {
        $coordinate_utils = new CoordinateUtils();
        $this->assertSame(
            5.0,
            $coordinate_utils->getDistance(
                ['x' => 0, 'y' => 1],
                ['x' => -3, 'y' => 5],
            ),
        );
    }
}
