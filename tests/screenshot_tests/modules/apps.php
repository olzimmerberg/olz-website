<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$apps_url = '/apps/';

function test_apps($driver, $base_url) {
    global $apps_url;
    tick('apps');

    test_apps_readonly($driver, $base_url);

    tock('apps', 'apps');
}

function test_apps_readonly($driver, $base_url) {
    global $apps_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$apps_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$apps_url}");
    take_pageshot($driver, 'apps_admin');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$apps_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$apps_url}");
    take_pageshot($driver, 'apps_vorstand');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$apps_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$apps_url}");
    take_pageshot($driver, 'apps_karten');
    logout($driver, $base_url);
}
