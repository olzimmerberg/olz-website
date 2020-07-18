<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$termine_url = '/?page=3';

function test_termine($driver, $base_url) {
    global $termine_url;
    $driver->get("{$base_url}{$termine_url}");
    take_pageshot($driver, 'termine');
}
