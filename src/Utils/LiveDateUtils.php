<?php

namespace Olz\Utils;

class LiveDateUtils extends AbstractDateUtils {
    public function getCurrentDateInFormat(string $format): string {
        return date($format);
    }
}
