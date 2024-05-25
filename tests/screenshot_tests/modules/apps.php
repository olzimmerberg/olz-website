<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Apps\OlzApps;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service/';

function test_apps(RemoteWebDriver $driver, string $base_url): void {
    global $service_url;
    tick('apps');

    test_apps_readonly($driver, $base_url);

    tock('apps', 'apps');
}

function test_apps_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $service_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'apps_admin');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'apps_vorstand');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'apps_karten');
    logout($driver, $base_url);

    login($driver, $base_url, 'benutzer', 'b3nu723r');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'apps_benutzer');
    logout($driver, $base_url);

    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'apps_anonym');

    login($driver, $base_url, 'admin', 'adm1n');
    $apps = OlzApps::getApps();
    foreach ($apps as $app) {
        $app_href = "/{$app->getHref()}";
        $app_basename = $app->getBasename();
        $driver->get("{$base_url}{$app_href}");
        $driver->navigate()->refresh();
        $driver->get("{$base_url}{$app_href}");
        take_pageshot($driver, "app_{$app_basename}");
    }
    logout($driver, $base_url);
}
