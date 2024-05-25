<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$verein_url = '/verein';

function test_verein(RemoteWebDriver $driver, string $base_url): void {
    global $verein_url;
    tick('verein');

    test_verein_readonly($driver, $base_url);

    tock('verein', 'verein');
}

function test_verein_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $verein_url;
    $driver->get("{$base_url}{$verein_url}");
    take_pageshot($driver, 'verein');

    $driver->get("{$base_url}{$verein_url}/praesi");
    take_pageshot($driver, 'verein_praesi');
}
