<?php

require_once __DIR__.'/AbstractDateUtils.php';

class LiveDateUtils extends AbstractDateUtils {
    public function getCurrentDateInFormat($format) {
        return date($format);
    }
}
