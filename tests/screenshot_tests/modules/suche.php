<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$suche_url = '/suche';
$neujahr_suche_url = "{$suche_url}?anfrage=neujahr";

function test_suche(RemoteWebDriver $driver, string $base_url): void {
    global $neujahr_suche_url;
    tick('suche');

    test_suche_readonly($driver, $base_url);

    tock('suche', 'suche');
}

function test_suche_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $neujahr_suche_url;
    $driver->get("{$base_url}{$neujahr_suche_url}");
    take_pageshot($driver, 'suche');
}
