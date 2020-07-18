<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$error_url = '/?page=0';

function test_error($driver, $base_url) {
    global $error_url;
    $driver->get("{$base_url}{$error_url}");
    take_pageshot($driver, 'error');
}
