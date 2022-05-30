<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/oev/utils/CoordinateUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \CoordinateUtils
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
