<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$aktuell_url = '/?page=2';
$aktuell_id_3_url = "{$aktuell_url}&id=3";

function test_aktuell($driver, $base_url) {
    global $aktuell_id_3_url;
    $driver->get("{$base_url}{$aktuell_id_3_url}");
    take_pageshot($driver, 'aktuell_id_3');
}
