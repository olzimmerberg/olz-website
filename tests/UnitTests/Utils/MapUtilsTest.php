<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MapUtils;

class TestOnlyMapUtils extends MapUtils {
    public function testOnlyDECtoSEX($angle) {
        return $this->DECtoSEX($angle);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\MapUtils
 */
final class MapUtilsTest extends UnitTestCase {
    public const GROSSMUENSTER_LAT = 47.37022;
    public const GROSSMUENSTER_LNG = 8.54377;
    public const GROSSMUENSTER_Y = 683471.0;
    public const GROSSMUENSTER_X = 247185.0;

    public function testDECtoSEX(): void {
        $map_utils = new TestOnlyMapUtils();
        $this->assertSame(0, $map_utils->testOnlyDECtoSEX(0));
        $this->assertSame(3600, $map_utils->testOnlyDECtoSEX(1));
        $this->assertSame(1800.0, $map_utils->testOnlyDECtoSEX(0.5));
    }

    public function testWGStoCH(): void {
        $map_utils = new MapUtils();
        $x = $map_utils->WGStoCHx(self::GROSSMUENSTER_LAT, self::GROSSMUENSTER_LNG);
        $y = $map_utils->WGStoCHy(self::GROSSMUENSTER_LAT, self::GROSSMUENSTER_LNG);
        $this->assertSame(self::GROSSMUENSTER_X, round($x));
        $this->assertSame(self::GROSSMUENSTER_Y, round($y));
    }

    public function testCHtoWGS(): void {
        $map_utils = new MapUtils();
        $lat = $map_utils->CHtoWGSlat(self::GROSSMUENSTER_Y, self::GROSSMUENSTER_X);
        $lng = $map_utils->CHtoWGSlng(self::GROSSMUENSTER_Y, self::GROSSMUENSTER_X);
        $this->assertSame(
            $this->roundToPlaces(self::GROSSMUENSTER_LAT, 4),
            $this->roundToPlaces($lat, 4),
        );
        $this->assertSame(
            $this->roundToPlaces(self::GROSSMUENSTER_LNG, 4),
            $this->roundToPlaces($lng, 4),
        );
    }

    public function roundToPlaces(float $number, int $places): float {
        $factor = pow(10, $places);
        return round($number * $factor) / $factor;
    }
}
