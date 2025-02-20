<?php

namespace Olz\Utils;

class FixedDateUtils extends AbstractDateUtils {
    private ?int $fixed_date = null;

    public function __construct(int|string $fixed_date) {
        $this->fixed_date = is_int($fixed_date)
            ? $fixed_date
            : (@strtotime($fixed_date) ?: null);
    }

    public function getCurrentDateInFormat(string $format): string {
        return date($format, $this->fixed_date);
    }
}
