<?php

namespace Olz\Utils;

class FixedDateUtils extends AbstractDateUtils {
    private $fixed_date;

    public function __construct($fixed_date) {
        $this->fixed_date = is_numeric($fixed_date) ? $fixed_date : strtotime($fixed_date);
    }

    public function getCurrentDateInFormat($format) {
        return date($format, $this->fixed_date);
    }
}
