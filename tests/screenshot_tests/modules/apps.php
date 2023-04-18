<?php

namespace Facebook\WebDriver;

use Olz\Apps\OlzApps;

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

    login($driver, $base_url, 'benutzer', 'b3nu723r');
    $driver->get("{$base_url}{$apps_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$apps_url}");
    take_pageshot($driver, 'apps_benutzer');
    logout($driver, $base_url);

    $driver->get("{$base_url}{$apps_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$apps_url}");
    take_pageshot($driver, 'apps_anonym');

    login($driver, $base_url, 'admin', 'adm1n');
    $apps = OlzApps::getApps();
    foreach ($apps as $app) {
        $app_href = "/{$app->getHref()}";
        $app_basename = $app->getBasename();
        $driver->get("{$base_url}{$app_href}");
        $driver->navigate()->refresh();
        $driver->get("{$base_url}{$app_href}");
        sleep(1);
        take_pageshot($driver, "app_{$app_basename}");
    }
    logout($driver, $base_url);
}
