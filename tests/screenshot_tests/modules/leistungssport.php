<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$leistungssport_url = '/blog.php';

function test_leistungssport($driver, $base_url) {
    global $leistungssport_url;
    $driver->get("{$base_url}{$leistungssport_url}");
    take_pageshot($driver, 'leistungssport');
}
