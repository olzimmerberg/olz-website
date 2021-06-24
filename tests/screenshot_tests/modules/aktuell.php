<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$aktuell_url = '/aktuell.php';
$aktuell_id_3_url = "{$aktuell_url}?id=3";

function test_aktuell($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;
    tick('aktuell');

    test_aktuell_readonly($driver, $base_url);

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-neuer-eintrag')
    );
    $new_button->click();
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltitel')
    );
    $title_input->sendKeys('Das Geschehnis');
    $teaser_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltext')
    );
    $teaser_input->sendKeys('Kleiner Teaser für den Artikel.');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltextlang')
    );
    $text_input->sendKeys("<BILD1>Detailierte Schilderung des Geschehnisses.\n<DATEI1 text='Artikel als PDF'>");
    $author_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuellautor')
    );
    $author_input->sendKeys('t.e., s.t.');
    $upload_inputs = $driver->findElements(
        WebDriverBy::cssSelector('input[type=file]')
    );
    $image_upload_input = $upload_inputs[0];
    $image_path = realpath(__DIR__.'/../../../src/icns/schilf.jpg');
    $image_upload_input->sendKeys($image_path);
    $file_upload_input = $upload_inputs[1];
    $document_path = realpath(__DIR__.'/../../../src/tools/dev-data/sample-data/sample-document.pdf');
    $file_upload_input->sendKeys($document_path);
    $driver->wait()->until(function () use ($driver) {
        $delete_buttons = $driver->findElements(
            WebDriverBy::cssSelector('img[title="löschen"]')
        );
        return count($delete_buttons) == 2;
    });
    take_pageshot($driver, 'aktuell_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'aktuell_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'aktuell_new_finished');

    $driver->get("{$base_url}{$aktuell_id_3_url}");

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#content_mitte .linkedit')
    );
    $edit_button->click();
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltextlang')
    );
    $text_input->sendKeys("\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'aktuell_update_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'aktuell_update_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'aktuell_update_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('aktuell', 'aktuell');
}

function test_aktuell_readonly($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;
    $driver->get("{$base_url}{$aktuell_url}");
    take_pageshot($driver, 'aktuell');
    $driver->get("{$base_url}{$aktuell_id_3_url}");
    take_pageshot($driver, 'aktuell_id_3');
}
