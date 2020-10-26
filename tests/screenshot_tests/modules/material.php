<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$material_url = '/material.php';

function test_material($driver, $base_url) {
    global $material_url;
    $driver->get("{$base_url}{$material_url}");
    take_pageshot($driver, 'material');
}
