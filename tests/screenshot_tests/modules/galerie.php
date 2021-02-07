<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$galerie_url = '/galerie.php';
$galerie_id_2_url = "{$galerie_url}?id=2";

function test_galerie($driver, $base_url) {
    global $galerie_id_2_url;
    test_galerie_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$galerie_id_2_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$galerie_id_2_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttongalerie-neue-galerie')
    );
    $new_button->click();

    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#galerietitel')
    );
    $title_input->sendKeys('Zweimal dasselbe Bild');
    $author_input = $driver->findElement(
        WebDriverBy::cssSelector('#galerieautor')
    );
    $author_input->sendKeys('bot');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('input[type=file]')
    );
    $big_image_path = realpath(__DIR__.'/../../../src/tools/dev-data/sample-data/sample-picture.jpg');
    $image_upload_input->sendKeys($big_image_path);
    $small_image_path = realpath(__DIR__.'/../../../src/icns/schilf.jpg');
    $image_upload_input->sendKeys($small_image_path);
    $driver->wait()->until(function () use ($driver) {
        $delete_buttons = $driver->findElements(
            WebDriverBy::cssSelector('img[title="löschen"]')
        );
        return count($delete_buttons) == 2;
    });
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

    reset_dev_data();
}

function test_galerie_readonly($driver, $base_url) {
    global $galerie_id_2_url;
    $driver->get("{$base_url}{$galerie_id_2_url}");
    take_pageshot($driver, 'galerie_id_2');
}
