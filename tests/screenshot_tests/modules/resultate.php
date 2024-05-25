<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$resultate_url = '/apps/resultate/?file=results.xml#/class0';

function test_resultate(RemoteWebDriver $driver, string $base_url): void {
    global $resultate_url;
    tick('resultate');

    test_resultate_readonly($driver, $base_url);

    tock('resultate', 'resultate');
}

function test_resultate_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $resultate_url;
    $driver->get("{$base_url}{$resultate_url}");
    $checkbox_0_elem = $driver->findElement(
        WebDriverBy::cssSelector('input#chk-0')
    );
    $checkbox_0_elem->click();
    $checkbox_1_elem = $driver->findElement(
        WebDriverBy::cssSelector('input#chk-1')
    );
    $checkbox_1_elem->click();

    take_pageshot($driver, 'resultate');
}
