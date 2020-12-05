<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';

function test_startseite($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');
}
