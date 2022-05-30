<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$aktuell_url = '/aktuell.php';
$aktuell_id_3_url = "{$aktuell_url}?id=3";

function test_aktuell($driver, $base_url) {
    tick('aktuell');

    test_aktuell_readonly($driver, $base_url);
    test_create_aktuell_old($driver, $base_url);
    test_create_aktuell_new($driver, $base_url);

    reset_dev_data();
    tock('aktuell', 'aktuell');
}

function test_create_aktuell_old($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-neuer-eintrag')
    );
    click($new_button);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltitel')
    );
    sendKeys($title_input, 'Das Geschehnis');
    $teaser_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltext')
    );
    sendKeys($teaser_input, 'Kleiner Teaser für den Artikel.');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltextlang')
    );
    sendKeys($text_input, "<BILD1>Detailierte Schilderung des Geschehnisses.\n<DATEI1 text='Artikel als PDF'>");
    $author_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuellautor')
    );
    sendKeys($author_input, 't.e., s.t.');
    $upload_inputs = $driver->findElements(
        WebDriverBy::cssSelector('input[type=file]')
    );
    $image_upload_input = $upload_inputs[0];
    $image_path = realpath(__DIR__.'/../../../public/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $file_upload_input = $upload_inputs[1];
    $document_path = realpath(__DIR__.'/../../../_/tools/dev-data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
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
    click($preview_button);
    take_pageshot($driver, 'aktuell_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'aktuell_new_finished');

    $driver->get("{$base_url}{$aktuell_id_3_url}");

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#content_mitte .linkedit')
    );
    click($edit_button);
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#aktuelltextlang')
    );
    sendKeys($text_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'aktuell_update_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-vorschau')
    );
    click($preview_button);
    take_pageshot($driver, 'aktuell_update_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonaktuell-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'aktuell_update_finished');

    logout($driver, $base_url);
}

function test_create_aktuell_new($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->executeScript('window.localStorage.setItem("FEATURES", "news")');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-title-input')
    );
    sendKeys($title_input, 'Das Geschehnis');
    $teaser_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-teaser-input')
    );
    sendKeys($teaser_input, 'Kleiner Teaser für den Artikel.');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-content-input')
    );
    sendKeys($content_input, "<BILD1>Detailierte Schilderung des Geschehnisses.\n<DATEI1 text='Artikel als PDF'>");
    $author_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input')
    );
    sendKeys($author_input, 't.e., s.t.');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../public/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#news-images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../_/tools/dev-data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#news-files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    // $driver->wait()->until(function () use ($driver) {
    //     $delete_buttons = $driver->findElements(
    //         WebDriverBy::cssSelector('img[title="löschen"]')
    //     );
    //     return count($delete_buttons) == 2;
    // });
    take_pageshot($driver, 'news_new_edit');

    // $preview_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttonaktuell-vorschau')
    // );
    // click($preview_button);
    // take_pageshot($driver, 'news_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_finished');

    $driver->get("{$base_url}{$aktuell_id_3_url}");

    // $edit_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#content_mitte .linkedit')
    // );
    // click($edit_button);
    // $text_input = $driver->findElement(
    //     WebDriverBy::cssSelector('#aktuelltextlang')
    // );
    // sendKeys($text_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    // take_pageshot($driver, 'news_update_edit');

    // $preview_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttonaktuell-vorschau')
    // );
    // click($preview_button);
    // take_pageshot($driver, 'news_update_preview');

    // $save_button = $driver->findElement(
    //     WebDriverBy::cssSelector('#buttonaktuell-speichern')
    // );
    // click($save_button);
    // take_pageshot($driver, 'news_update_finished');

    logout($driver, $base_url);
}

function test_aktuell_readonly($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;
    $driver->get("{$base_url}{$aktuell_url}");
    take_pageshot($driver, 'aktuell');
    $driver->get("{$base_url}{$aktuell_id_3_url}");
    take_pageshot($driver, 'aktuell_id_3');
}
