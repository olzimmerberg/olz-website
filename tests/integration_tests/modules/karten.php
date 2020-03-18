<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$karten_url = '/?page=12';

function test_karten($driver, $base_url) {
    global $karten_url;
    $driver->get("{$base_url}{$karten_url}");
    take_pageshot($driver, 'karten');
}
