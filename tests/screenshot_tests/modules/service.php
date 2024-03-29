<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service';

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

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service_authenticated');
}
