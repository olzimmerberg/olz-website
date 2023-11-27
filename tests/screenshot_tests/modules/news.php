<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$news_url = '/news';
$news_id_3_url = "{$news_url}/3";
$news_id_5_url = "{$news_url}/5";
$news_id_8_url = "{$news_url}/8";
$news_id_10_url = "{$news_url}/10";

function test_news($driver, $base_url) {
    tick('news');

    test_news_readonly($driver, $base_url);
    test_create_aktuell_new($driver, $base_url);
    test_create_kaderblog_new($driver, $base_url);
    test_create_anonymous_new($driver, $base_url);
    test_create_forum_new($driver, $base_url);
    test_create_galerie_new($driver, $base_url);

    reset_dev_data();
    tock('news', 'news');
}

function test_create_aktuell_new($driver, $base_url) {
    global $news_url, $news_id_5_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$news_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$news_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #format-input')
    ));
    $format_select->selectByVisibleText('Aktuell');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, 'Das Geschehnis');
    $teaser_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #teaser-input')
    );
    sendKeys($teaser_input, 'Kleiner Teaser für den Artikel.');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "<BILD1>Detailierte Schilderung des Geschehnisses.\n<DATEI1 text='Artikel als PDF'>");

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'news_new_aktuell_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_aktuell_finished');

    $driver->get("{$base_url}{$news_id_5_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_aktuell_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_aktuell_finished');

    logout($driver, $base_url);
}

function test_create_kaderblog_new($driver, $base_url) {
    global $news_url, $news_id_10_url;

    login($driver, $base_url, 'kaderlaeufer', 'kad3rla3uf3r');
    $driver->get("{$base_url}{$news_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$news_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #role-index-0')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #format-input')
    ));
    $format_select->selectByVisibleText('Kaderblog');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, 'Das Training');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "<BILD1>Detailierte Schilderung des Trainings.\n<DATEI1 text='Artikel als PDF'>");

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'news_new_kaderblog_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_kaderblog_finished');

    $driver->get("{$base_url}{$news_id_10_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_kaderblog_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_kaderblog_finished');

    logout($driver, $base_url);
}

function test_create_anonymous_new($driver, $base_url) {
    global $news_url, $news_id_5_url;

    logout($driver, $base_url);
    $driver->get("{$base_url}{$news_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$news_url}");

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
        WebDriverBy::cssSelector('#edit-news-modal #authorName-input')
    );
    sendKeys($author_input, 'Anonymous Integration Test');

    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, 'Der Eintrag');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "Der Inhalt des Eintrags");
    $recaptcha_consent_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #recaptcha-consent-given-input')
    );
    click($recaptcha_consent_input);
    sleep(random_int(2, 6));
    usleep(random_int(0, 999999));

    take_pageshot($driver, 'news_new_anonymous_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_anonymous_finished');
}

function test_create_forum_new($driver, $base_url) {
    global $news_url, $news_id_8_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$news_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$news_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #format-input')
    ));
    $format_select->selectByVisibleText('Forum');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, 'Der Eintrag');
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "Der Inhalt des Eintrags");

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    take_pageshot($driver, 'news_new_forum_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_forum_finished');

    $driver->get("{$base_url}{$news_id_8_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $content_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #content-input')
    );
    sendKeys($content_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_forum_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_forum_finished');

    logout($driver, $base_url);
}

function test_create_galerie_new($driver, $base_url) {
    global $news_url, $news_id_5_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$news_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$news_url}");

    $create_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-news-button')
    );
    click($create_news_button);
    sleep(1);
    $author_dropdown = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton')
    );
    click($author_dropdown);
    $author_choice = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #authorUserId-authorRoleId-field #role-index-1')
    );
    click($author_choice);
    $format_select = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #format-input')
    ));
    $format_select->selectByVisibleText('Galerie');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, 'Das Fotoshooting');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    take_pageshot($driver, 'news_new_galerie_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_new_galerie_finished');

    $driver->get("{$base_url}{$news_id_5_url}");

    $edit_news_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-button')
    );
    click($edit_news_button);
    sleep(1);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #title-input')
    );
    sendKeys($title_input, "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
    take_pageshot($driver, 'news_update_galerie_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-news-modal #submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'news_update_galerie_finished');

    logout($driver, $base_url);
}

function test_news_readonly($driver, $base_url) {
    global $news_url, $news_id_3_url;
    $driver->get("{$base_url}{$news_url}");
    take_pageshot($driver, 'news');
    $driver->get("{$base_url}{$news_id_3_url}");
    take_pageshot($driver, 'news_id_3');
}
