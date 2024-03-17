<?php

namespace Olz\Utils;

class LiveDateUtils extends AbstractDateUtils {
    public function getCurrentDateInFormat($format) {
        return date($format);
    }
}
