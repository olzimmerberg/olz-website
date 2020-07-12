<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$trophy_url = '/?page=20';

function test_trophy($driver, $base_url) {
    global $trophy_url;
    $driver->get("{$base_url}{$trophy_url}");
    take_pageshot($driver, 'trophy');
}
