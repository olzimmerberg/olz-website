<?php

namespace Olz\Utils;

class LiveDateUtils extends AbstractDateUtils {
    public const UTILS = [];

    public function getCurrentDateInFormat($format) {
        return date($format);
    }
}
