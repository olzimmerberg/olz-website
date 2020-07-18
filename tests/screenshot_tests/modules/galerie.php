<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$galerie_url = '/?page=4';
$galerie_id_2_url = "{$galerie_url}&id=2";

function test_galerie($driver, $base_url) {
    global $galerie_id_2_url;
    $driver->get("{$base_url}{$galerie_id_2_url}");
    take_pageshot($driver, 'galerie_id_2');
}
