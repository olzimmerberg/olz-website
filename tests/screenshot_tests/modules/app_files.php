<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$app_files_url = '/apps/files/';

function test_app_files(RemoteWebDriver $driver, string $base_url): void {
    global $app_files_url;
    tick('app_files');

    test_app_files_readonly($driver, $base_url);

    tock('app_files', 'app_files');
}

function test_app_files_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $app_files_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$app_files_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$app_files_url}");
    take_pageshot($driver, 'app_files_admin');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$app_files_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$app_files_url}");
    take_pageshot($driver, 'app_files_vorstand');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$app_files_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$app_files_url}");
    take_pageshot($driver, 'app_files_karten');
    logout($driver, $base_url);

    login($driver, $base_url, 'benutzer', 'b3nu723r');
    $driver->get("{$base_url}{$app_files_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$app_files_url}");
    take_pageshot($driver, 'app_files_benutzer');
    logout($driver, $base_url);

    $driver->get("{$base_url}{$app_files_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$app_files_url}");
    take_pageshot($driver, 'app_files_anonym');
}
