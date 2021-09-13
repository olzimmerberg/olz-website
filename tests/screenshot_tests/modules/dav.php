<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$dav_url = '/dav/server.php';

function test_dav($driver, $base_url) {
    global $dav_url;
    tick('dav');

    test_dav_readonly($driver, $base_url);

    tock('dav', 'dav');
}

function test_dav_readonly($driver, $base_url) {
    global $dav_url;

    $token_path_component = 'access_token__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    $driver->get("{$base_url}{$dav_url}/{$token_path_component}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$dav_url}/{$token_path_component}");
    take_pageshot($driver, 'dav_admin_token');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$dav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$dav_url}");
    take_pageshot($driver, 'dav_admin_php_session');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$dav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$dav_url}");
    // Throws fatal error :/
    // take_pageshot($driver, 'dav_vorstand_php_session');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$dav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$dav_url}");
    // Throws fatal error :/
    // take_pageshot($driver, 'dav_karten_php_session');
    logout($driver, $base_url);
}
