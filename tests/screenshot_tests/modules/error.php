<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$error_url = '/error.php';

function test_error($driver, $base_url) {
    global $error_url;
    tick('error');

    test_error_readonly($driver, $base_url);

    tock('error', 'error');
}

function test_error_readonly($driver, $base_url) {
    global $error_url;
    $driver->get("{$base_url}{$error_url}");
    take_pageshot($driver, 'error');
}
