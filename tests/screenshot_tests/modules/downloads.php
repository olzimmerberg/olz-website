<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service.php';

function test_downloads($driver, $base_url) {
    global $service_url;
    tick('downloads');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttondownloads-neuer-eintrag')
    );
    $new_button->click();
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#downloadsname')
    );
    $name_input->sendKeys('Neues Jahresprogramm');
    take_pageshot($driver, 'downloads_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttondownloads-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'downloads_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttondownloads-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'downloads_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('downloads', 'downloads');
}
