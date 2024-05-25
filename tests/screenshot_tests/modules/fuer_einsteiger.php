<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$fuer_einsteiger_url = '/fuer_einsteiger';

function test_fuer_einsteiger(RemoteWebDriver $driver, string $base_url): void {
    global $fuer_einsteiger_url;
    tick('fuer_einsteiger');

    test_fuer_einsteiger_readonly($driver, $base_url);

    tock('fuer_einsteiger', 'fuer_einsteiger');
}

function test_fuer_einsteiger_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $fuer_einsteiger_url;
    $driver->get("{$base_url}{$fuer_einsteiger_url}");
    take_pageshot($driver, 'fuer_einsteiger');
}
