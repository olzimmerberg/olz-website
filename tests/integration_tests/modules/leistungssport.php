<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$leistungssport_url = '/?page=7';

function test_leistungssport($driver, $base_url) {
    global $leistungssport_url;
    $driver->get("{$base_url}{$leistungssport_url}");
    take_pageshot($driver, 'leistungssport');
}
