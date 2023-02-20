<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$aktuell_url = '/aktuell.php';
$aktuell_id_3_url = "{$aktuell_url}?id=3";
$aktuell_id_5_url = "{$aktuell_url}?id=5";
$aktuell_id_8_url = "{$aktuell_url}?id=8";

function test_aktuell($driver, $base_url) {
    tick('aktuell');

    test_aktuell_readonly($driver, $base_url);
    test_create_aktuell_new($driver, $base_url);
    test_create_anonymous_new($driver, $base_url);
    test_create_forum_new($driver, $base_url);
    test_create_galerie_new($driver, $base_url);

    reset_dev_data();
    tock('aktuell', 'aktuell');
}

function test_create_aktuell_new($driver, $base_url) {
    global $aktuell_url, $aktuell_id_5_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#news-format-input')
    ));
    $format_select->selectByVisibleText('Aktuell');
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
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#news-files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'news_new_aktuell_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_aktuell_finished');

    $driver->get("{$base_url}{$aktuell_id_5_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-content-input')
    );
    sendKeys($content_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_aktuell_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_aktuell_finished');

    logout($driver, $base_url);
}

function test_create_anonymous_new($driver, $base_url) {
    global $aktuell_url, $aktuell_id_5_url;

    logout($driver, $base_url);
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    $create_anonymous_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-anonymous-button')
    );
    click($create_anonymous_button);
    sleep(1);
    $author_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-name-input')
    );
    sendKeys($author_input, 'Anonymous Integration Test');

    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-title-input')
    );
    sendKeys($title_input, 'Der Eintrag');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-content-input')
    );
    sendKeys($content_input, "Der Inhalt des Eintrags");
    $recaptcha_consent_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-recaptcha-consent-given-input')
    );
    click($recaptcha_consent_input);
    sleep(random_int(2, 6));
    usleep(random_int(0, 999999));

    take_pageshot($driver, 'news_new_anonymous_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_anonymous_finished');
}

function test_create_forum_new($driver, $base_url) {
    global $aktuell_url, $aktuell_id_8_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#news-format-input')
    ));
    $format_select->selectByVisibleText('Forum');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-title-input')
    );
    sendKeys($title_input, 'Der Eintrag');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-content-input')
    );
    sendKeys($content_input, "Der Inhalt des Eintrags");

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

    take_pageshot($driver, 'news_new_forum_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_forum_finished');

    $driver->get("{$base_url}{$aktuell_id_8_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-content-input')
    );
    sendKeys($content_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_forum_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_forum_finished');

    logout($driver, $base_url);
}

function test_create_galerie_new($driver, $base_url) {
    global $aktuell_url, $aktuell_id_5_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$aktuell_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$aktuell_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#news-author-input #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#news-format-input')
    ));
    $format_select->selectByVisibleText('Galerie');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-title-input')
    );
    sendKeys($title_input, 'Das Fotoshooting');

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

    take_pageshot($driver, 'news_new_galerie_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_galerie_finished');

    $driver->get("{$base_url}{$aktuell_id_5_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#news-title-input')
    );
    sendKeys($title_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_galerie_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_galerie_finished');

    logout($driver, $base_url);
}

function test_aktuell_readonly($driver, $base_url) {
    global $aktuell_url, $aktuell_id_3_url;
    $driver->get("{$base_url}{$aktuell_url}");
    take_pageshot($driver, 'aktuell');
    $driver->get("{$base_url}{$aktuell_id_3_url}");
    take_pageshot($driver, 'aktuell_id_3');
}
