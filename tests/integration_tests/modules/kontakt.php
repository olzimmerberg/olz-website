<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$kontakt_url = '/?page=6';

function test_kontakt($driver, $base_url) {
    global $kontakt_url;
    $driver->get("{$base_url}{$kontakt_url}");
    take_pageshot($driver, 'kontakt');
}
