<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$fragen_und_antworten_url = '/fragen_und_antworten.php';

function test_fragen_und_antworten($driver, $base_url) {
    global $fragen_und_antworten_url;
    test_fragen_und_antworten_readonly($driver, $base_url);
}

function test_fragen_und_antworten_readonly($driver, $base_url) {
    global $fragen_und_antworten_url;
    $driver->get("{$base_url}{$fragen_und_antworten_url}");
    take_pageshot($driver, 'fragen_und_antworten');
}
