<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service.php';

function test_service($driver, $base_url) {
    global $service_url;
    tick('service');

    test_service_readonly($driver, $base_url);

    tock('service', 'service');
}

function test_service_readonly($driver, $base_url) {
    global $service_url;
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service');
}
