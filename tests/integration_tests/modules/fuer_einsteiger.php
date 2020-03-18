<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$fuer_einsteiger_url = '/?page=18';

function test_fuer_einsteiger($driver, $base_url) {
    global $fuer_einsteiger_url;
    $driver->get("{$base_url}{$fuer_einsteiger_url}");
    take_pageshot($driver, 'fuer_einsteiger');
}
