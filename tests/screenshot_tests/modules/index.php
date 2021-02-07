<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$index_url = '/index.php';
$index_with_page_url = "{$index_url}?page=3";

function test_index($driver, $base_url) {
    global $index_url, $index_with_page_url;
    $driver->get("{$base_url}{$index_url}");
    take_pageshot($driver, 'index_without_page');

    $driver->get("{$base_url}{$index_with_page_url}");
    take_pageshot($driver, 'index_with_page');
}
