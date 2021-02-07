<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$kontakt_url = '/kontakt.php';

function test_kontakt($driver, $base_url) {
    global $kontakt_url;
    tick('kontakt');

    test_kontakt_readonly($driver, $base_url);

    tock('kontakt', 'kontakt');
}

function test_kontakt_readonly($driver, $base_url) {
    global $kontakt_url;
    $driver->get("{$base_url}{$kontakt_url}");
    take_pageshot($driver, 'kontakt');
}
