<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$search_url = '/search.php';

function test_search($driver, $base_url) {
    global $search_url;
    tick('search');

    test_search_readonly($driver, $base_url);

    tock('search', 'search');
}

function test_search_readonly($driver, $base_url) {
    global $search_url;
    $driver->get("{$base_url}{$search_url}");
    take_pageshot($driver, 'search');
}
