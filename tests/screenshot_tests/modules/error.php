<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$error_url = '/error';

function test_error(RemoteWebDriver $driver, string $base_url): void {
    global $error_url;
    tick('error');

    test_error_readonly($driver, $base_url);

    tock('error', 'error');
}

function test_error_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $error_url;

    $driver->get("{$base_url}{$error_url}/400");
    take_pageshot($driver, 'error_400');

    $driver->get("{$base_url}{$error_url}/401");
    take_pageshot($driver, 'error_401');

    $driver->get("{$base_url}{$error_url}/403");
    take_pageshot($driver, 'error_403');

    $driver->get("{$base_url}{$error_url}/404");
    take_pageshot($driver, 'error_404');

    $driver->get("{$base_url}{$error_url}/500");
    take_pageshot($driver, 'error_500');

    $driver->get("{$base_url}{$error_url}/529");
    take_pageshot($driver, 'error_529');
}
