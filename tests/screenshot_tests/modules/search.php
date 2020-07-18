<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$search_url = '/?page=9';

function test_search($driver, $base_url) {
    global $search_url;
    $driver->get("{$base_url}{$search_url}");
    take_pageshot($driver, 'search');
}
