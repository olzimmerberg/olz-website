<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$galerie_url = '/galerie.php';
$galerie_id_2_url = "{$galerie_url}?id=2";

function test_galerie($driver, $base_url) {
    global $galerie_id_2_url;
    $driver->get("{$base_url}{$galerie_id_2_url}");
    take_pageshot($driver, 'galerie_id_2');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$galerie_id_2_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$galerie_id_2_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttongalerie-neue-galerie')
    );
    $new_button->click();
    take_pageshot($driver, 'galerie_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttongalerie-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'galerie_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttongalerie-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'galerie_new_finished');

    logout($driver, $base_url);
}
