<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$trophy_url = '/trophy';

function test_trophy(RemoteWebDriver $driver, string $base_url): void {
    global $trophy_url;
    tick('trophy');

    test_trophy_readonly($driver, $base_url);

    tock('trophy', 'trophy');
}

function test_trophy_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $trophy_url;
    $driver->get("{$base_url}{$trophy_url}");
    take_pageshot($driver, 'trophy');
}
