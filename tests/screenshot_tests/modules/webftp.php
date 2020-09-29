<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$webftp_url = '/?page=ftp';

function test_webftp($driver, $base_url) {
    global $webftp_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$webftp_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webftp_url}");
    take_pageshot($driver, 'webftp_admin');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$webftp_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webftp_url}");
    take_pageshot($driver, 'webftp_vorstand');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$webftp_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webftp_url}");
    take_pageshot($driver, 'webftp_karten');
    logout($driver, $base_url);
}
