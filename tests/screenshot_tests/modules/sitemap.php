<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$sitemap_url = '/sitemap';

function test_sitemap(RemoteWebDriver $driver, string $base_url): void {
    global $sitemap_url;
    tick('sitemap');

    test_sitemap_readonly($driver, $base_url);

    tock('sitemap', 'sitemap');
}

function test_sitemap_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $sitemap_url;
    $driver->get("{$base_url}{$sitemap_url}");
    take_pageshot($driver, 'sitemap');
}
