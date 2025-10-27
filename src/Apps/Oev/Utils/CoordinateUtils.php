<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;

class CoordinateUtils {
    use WithUtilsTrait;

    /**
     * @param array<array{x: int|float, y: int|float}> $points
     *
     * @return array{x: int|float, y: int|float}
     */
    public function getCenter(array $points): array {
        $sum_x = 0;
        $sum_y = 0;
        foreach ($points as $point) {
            $sum_x += $point['x'];
            $sum_y += $point['y'];
        }
        return [
            'x' => $sum_x / count($points),
            'y' => $sum_y / count($points),
        ];
    }

    /**
     * @param array{x: int|float, y: int|float} $point_a
     * @param array{x: int|float, y: int|float} $point_b
     */
    public function getDistance(array $point_a, array $point_b): float {
        $x_diff = $point_a['x'] - $point_b['x'];
        $y_diff = $point_a['y'] - $point_b['y'];
        return sqrt($x_diff * $x_diff + $y_diff * $y_diff);
    }
}
