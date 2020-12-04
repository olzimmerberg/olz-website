<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$karten_url = '/karten.php';

function test_karten($driver, $base_url) {
    global $karten_url;
    $driver->get("{$base_url}{$karten_url}");
    take_pageshot($driver, 'karten');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$karten_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$karten_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonkarten-neue-karte')
    );
    $new_button->click();
    take_pageshot($driver, 'karten_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonkarten-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'karten_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonkarten-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'karten_new_finished');

    logout($driver, $base_url);
}
