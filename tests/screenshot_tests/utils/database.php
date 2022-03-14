<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/timing.php';

function reset_dev_data() {
    global $base_url;
    tick('reset');
    for ($i = 0; $i < 50; $i++) {
        $result = file_get_contents("{$base_url}tools.php/reset");
        if ($result == 'reset:SUCCESS') {
            tock('reset', 'db_reset');
            return;
        }
        sleep(0.5);
    }
    throw new Exception("Resetting dev data timed out");
}
