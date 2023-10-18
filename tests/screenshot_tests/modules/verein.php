<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$verein_url = '/verein';

function test_verein($driver, $base_url) {
    global $verein_url;
    tick('verein');

    test_verein_readonly($driver, $base_url);

    tock('verein', 'verein');
}

function test_verein_readonly($driver, $base_url) {
    global $verein_url;
    $driver->get("{$base_url}{$verein_url}");
    take_pageshot($driver, 'verein');

    $driver->get("{$base_url}{$verein_url}/praesi");
    take_pageshot($driver, 'verein_praesi');
}
