<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';

function test_startseite($driver, $base_url) {
    global $startseite_url;
    test_startseite_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $edit_link = $driver->findElement(
        WebDriverBy::cssSelector('#olz-text-edit-22')
    );
    $edit_link->click();
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#olz_texttext')
    );
    $text_input->sendKeys('Neue Information!');
    take_pageshot($driver, 'startseite_banner_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonolz_text-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'startseite_banner_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonolz_text-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'startseite_banner_finished');

    logout($driver, $base_url);

    reset_dev_data();
}

function test_startseite_readonly($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');
}
