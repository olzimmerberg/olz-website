<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$divmail_url = '/divmail.php';

function test_divmail($driver, $base_url) {
    global $divmail_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$divmail_url}");
    take_pageshot($driver, 'divmail');
    logout($driver, $base_url);
}
