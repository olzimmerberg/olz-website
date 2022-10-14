<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/timing.php';

function full_reset_dev_data() {
    global $base_url;
    tick('full_reset');
    for ($i = 0; $i < 1000; $i++) {
        $result = file_get_contents("{$base_url}/tools.php/full-reset");
        if ($result == 'full-reset:SUCCESS') {
            tock('full_reset', 'db_full_reset');
            return;
        }
        usleep(100 * 1000);
    }
    throw new \Exception("Fully resetting dev data timed out");
}

function reset_dev_data() {
    global $base_url;
    tick('reset');
    for ($i = 0; $i < 100; $i++) {
        $result = file_get_contents("{$base_url}/tools.php/reset-content");
        if ($result == 'reset-content:SUCCESS') {
            tock('reset', 'db_reset');
            return;
        }
        usleep(100 * 1000);
    }
    throw new \Exception("Resetting dev data timed out");
}
