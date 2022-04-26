<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$zimmerberg_ol_url = '/zimmerberg_ol/';

function test_zimmerberg_ol($driver, $base_url) {
    tick('zimmerberg_ol');

    test_zimmerberg_ol_readonly($driver, $base_url);

    tock('zimmerberg_ol', 'zimmerberg_ol');
}

function test_zimmerberg_ol_readonly($driver, $base_url) {
    global $zimmerberg_ol_url;

    $driver->get("{$base_url}{$zimmerberg_ol_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$zimmerberg_ol_url}");
    take_pageshot($driver, 'zimmerberg_ol');
}
