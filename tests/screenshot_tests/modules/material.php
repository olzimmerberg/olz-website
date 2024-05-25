<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$material_url = '/material';

function test_material(RemoteWebDriver $driver, string $base_url): void {
    global $material_url;
    tick('material');

    test_material_readonly($driver, $base_url);

    tock('material', 'material');
}

function test_material_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $material_url;
    $driver->get("{$base_url}{$material_url}");
    take_pageshot($driver, 'material');
}
