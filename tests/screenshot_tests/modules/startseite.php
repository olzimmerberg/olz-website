<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';

function test_startseite($driver, $base_url) {
    global $startseite_url;
    tick('startseite');

    test_startseite_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text .olz-edit-button')
    );
    $edit_button->click();
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text textarea[name="text"]')
    );
    $text_input->sendKeys('Neue Information!');
    take_pageshot($driver, 'startseite_banner_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text .olz-edit-submit')
    );
    $save_button->click();
    take_pageshot($driver, 'startseite_banner_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('startseite', 'startseite');
}

function test_startseite_readonly($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');
}
