<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$fragen_und_antworten_url = '/fragen_und_antworten';

function test_fragen_und_antworten(RemoteWebDriver $driver, string $base_url): void {
    global $fragen_und_antworten_url;
    tick('fragen_und_antworten');

    test_fragen_und_antworten_readonly($driver, $base_url);

    tock('fragen_und_antworten', 'fragen_und_antworten');
}

function test_fragen_und_antworten_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $fragen_und_antworten_url;
    $driver->get("{$base_url}{$fragen_und_antworten_url}");
    take_pageshot($driver, 'fragen_und_antworten');
}
