<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$leistungssport_url = '/blog.php';

function test_leistungssport($driver, $base_url) {
    global $leistungssport_url;
    tick('leistungssport');

    test_leistungssport_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$leistungssport_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$leistungssport_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-neuer-eintrag')
    );
    click($new_button);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#blogtitel')
    );
    sendKeys($title_input, 'Ich bin Administrator-Meister!');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#blogtext')
    );
    sendKeys($text_input, 'Wie es dazu kam.');
    take_pageshot($driver, 'leistungssport_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-vorschau')
    );
    click($preview_button);
    take_pageshot($driver, 'leistungssport_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonblog-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'leistungssport_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('leistungssport', 'leistungssport');
}

function test_leistungssport_readonly($driver, $base_url) {
    global $leistungssport_url;
    $driver->get("{$base_url}{$leistungssport_url}");
    take_pageshot($driver, 'leistungssport');
}
