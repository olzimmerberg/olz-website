<?php

require_once __DIR__.'/AbstractDateUtils.php';

class LiveDateUtils extends AbstractDateUtils {
    public const UTILS = [];

    public function getCurrentDateInFormat($format) {
        return date($format);
    }
}
