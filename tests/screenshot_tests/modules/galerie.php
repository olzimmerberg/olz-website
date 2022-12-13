<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$galerie_url = '/galerie.php';
$galerie_id_2_url = "{$galerie_url}?id=2";

function test_galerie($driver, $base_url) {
    global $galerie_id_2_url;
    tick('galerie');

    test_galerie_readonly($driver, $base_url);

    reset_dev_data();
    tock('galerie', 'galerie');
}

function test_galerie_readonly($driver, $base_url) {
    global $galerie_id_2_url;
    $driver->get("{$base_url}{$galerie_id_2_url}");
    take_pageshot($driver, 'galerie_id_2');
}
