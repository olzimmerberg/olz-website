<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$webdav_url = '/webdav/server.php';

function test_webdav($driver, $base_url) {
    global $webdav_url;
    tick('webdav');

    test_webdav_readonly($driver, $base_url);

    tock('webdav', 'webdav');
}

function test_webdav_readonly($driver, $base_url) {
    global $webdav_url;

    $declare_flaky_elements = <<<'ZZZZZZZZZZ'
        const columnSelector = '.nodeTable tr td:not(.nameColumn):not(.typeColumn)';
        const propTableSelector = '.propTable';
        [
            ...document.querySelectorAll(columnSelector),
            ...document.querySelectorAll(propTableSelector),
        ].map(elem => elem.classList.add('test-flaky'));
    ZZZZZZZZZZ;

    $token_path_component = 'access_token__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    $driver->get("{$base_url}{$webdav_url}/{$token_path_component}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webdav_url}/{$token_path_component}");
    $driver->executeScript($declare_flaky_elements);
    take_pageshot($driver, 'webdav_admin_token');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$webdav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webdav_url}");
    $driver->executeScript($declare_flaky_elements);
    take_pageshot($driver, 'webdav_admin_php_session');
    logout($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$webdav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webdav_url}");
    $driver->executeScript($declare_flaky_elements);
    take_pageshot($driver, 'webdav_vorstand_php_session');
    logout($driver, $base_url);

    login($driver, $base_url, 'karten', 'kar73n');
    $driver->get("{$base_url}{$webdav_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webdav_url}");
    $driver->executeScript($declare_flaky_elements);
    take_pageshot($driver, 'webdav_karten_php_session');
    logout($driver, $base_url);
}
