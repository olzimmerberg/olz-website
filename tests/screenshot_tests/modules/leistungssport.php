<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$leistungssport_url = '/blog.php';

function test_leistungssport($driver, $base_url) {
    global $leistungssport_url;
    test_leistungssport_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$leistungssport_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$leistungssport_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-neuer-eintrag')
    );
    $new_button->click();
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#blogtitel')
    );
    $title_input->sendKeys('Ich bin Administrator-Meister!');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#blogtext')
    );
    $text_input->sendKeys('Wie es dazu kam.');
    take_pageshot($driver, 'leistungssport_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'leistungssport_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'leistungssport_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
}

function test_leistungssport_readonly($driver, $base_url) {
    global $leistungssport_url;
    $driver->get("{$base_url}{$leistungssport_url}");
    take_pageshot($driver, 'leistungssport');
}
