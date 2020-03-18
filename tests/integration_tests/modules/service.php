<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/?page=8';

function test_service($driver, $base_url) {
    global $service_url;
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service');
}
