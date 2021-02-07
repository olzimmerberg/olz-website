<?php

namespace Facebook\WebDriver;

function reset_dev_data() {
    global $base_url;
    for ($i = 0; $i < 50; $i++) {
        $result = file_get_contents("{$base_url}tools.php/reset");
        if ($result == 'reset:SUCCESS') {
            return;
        }
        sleep(0.5);
    }
    throw new Exception("Resetting dev data timed out");
}

function init_test_block($driver) {
    reset_dev_data();

    set_window_size($driver, 1280, 1024);
}
