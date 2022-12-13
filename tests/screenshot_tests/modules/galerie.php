<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$galerie_url = '/galerie.php';
$galerie_id_2_url = "{$galerie_url}?id=2";

function test_galerie($driver, $base_url) {
    global $galerie_id_2_url;
    tick('galerie');

    test_galerie_readonly($driver, $base_url);

    // login($driver, $base_url, 'admin', 'adm1n');
    // $driver->get("{$base_url}{$galerie_id_2_url}");
    // $driver->navigate()->refresh();
    // $driver->get("{$base_url}{$galerie_id_2_url}");

    // $new_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttongalerie-neue-galerie')
    // );
    // click($new_button);

    // $title_input = $driver->findElement(
    //     WebDriverBy::cssSelector('#galerietitel')
    // );
    // sendKeys($title_input, 'Zweimal dasselbe Bild');
    // $author_input = $driver->findElement(
    //     WebDriverBy::cssSelector('#galerieautor')
    // );
    // sendKeys($author_input, 'bot');

    // $image_upload_input = $driver->findElement(
    //     WebDriverBy::cssSelector('input[type=file]')
    // );
    // $big_image_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg');
    // sendKeys($image_upload_input, $big_image_path);
    // $driver->wait()->until(function () use ($driver) {
    //     $delete_buttons = $driver->findElements(
    //         WebDriverBy::cssSelector('img[title="löschen"]')
    //     );
    //     return count($delete_buttons) == 1;
    // });
    // $image_upload_input = $driver->findElement(
    //     WebDriverBy::cssSelector('input[type=file]')
    // );
    // $small_image_path = realpath(__DIR__.'/../../../public/icns/schilf.jpg');
    // sendKeys($image_upload_input, $small_image_path);
    // $driver->wait()->until(function () use ($driver) {
    //     $delete_buttons = $driver->findElements(
    //         WebDriverBy::cssSelector('img[title="löschen"]')
    //     );
    //     return count($delete_buttons) == 2;
    // });
    // take_pageshot($driver, 'galerie_new_edit');

    // $preview_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttongalerie-vorschau')
    // );
    // click($preview_button);
    // take_pageshot($driver, 'galerie_new_preview');

    // $save_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttongalerie-speichern')
    // );
    // click($save_button);
    // take_pageshot($driver, 'galerie_new_finished');

    // logout($driver, $base_url);

    reset_dev_data();
    tock('galerie', 'galerie');
}

function test_galerie_readonly($driver, $base_url) {
    global $galerie_id_2_url;
    $driver->get("{$base_url}{$galerie_id_2_url}");
    take_pageshot($driver, 'galerie_id_2');
}
