<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$termine_url = '/termine.php';

function test_termine($driver, $base_url) {
    global $termine_url;
    tick('termine');

    test_termine_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$termine_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$termine_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttontermine-neuer-eintrag')
    );
    $new_button->click();
    take_pageshot($driver, 'termine_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttontermine-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'termine_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttontermine-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'termine_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('termine', 'termine');
}

function test_termine_readonly($driver, $base_url) {
    global $termine_url;
    $driver->get("{$base_url}{$termine_url}");
    take_pageshot($driver, 'termine');

    $show_past_checkbox = $driver->findElement(
        WebDriverBy::cssSelector('#show-past-checkbox')
    );
    $show_past_checkbox->click();
    take_pageshot($driver, 'termine_past');
}
