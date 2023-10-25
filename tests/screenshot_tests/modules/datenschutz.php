<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$datenschutz_url = '/datenschutz';

function test_datenschutz($driver, $base_url) {
    global $datenschutz_url;
    tick('datenschutz');

    test_datenschutz_readonly($driver, $base_url);

    tock('datenschutz', 'datenschutz');
}

function test_datenschutz_readonly($driver, $base_url) {
    global $datenschutz_url;
    $driver->get("{$base_url}{$datenschutz_url}");
    take_pageshot($driver, 'datenschutz');
}
