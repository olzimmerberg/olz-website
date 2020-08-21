<?php

require_once __DIR__.'/DateUtils.php';

class LiveDateUtils extends DateUtils {
    public function getCurrentDateInFormat($format) {
        return date($format);
    }
}
