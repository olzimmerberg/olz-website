<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;

class CoordinateUtils {
    use WithUtilsTrait;

    public function getCenter($points) {
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

    public function getDistance($point_a, $point_b) {
        $x_diff = $point_a['x'] - $point_b['x'];
        $y_diff = $point_a['y'] - $point_b['y'];
        return sqrt($x_diff * $x_diff + $y_diff * $y_diff);
    }

    public static function fromEnv(): self {
        return new self();
    }
}
