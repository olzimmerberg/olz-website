<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$startseite_url = '/';

function test_startseite(RemoteWebDriver $driver, string $base_url): void {
    global $startseite_url;
    tick('startseite');

    test_startseite_readonly($driver, $base_url);

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#edit-snippet-modal #submit-button')
    );
    click($save_button);
    $driver->wait()->until(function () use ($driver) {
        $rendered_html = $driver->findElement(
            WebDriverBy::cssSelector('#important-banner .olz-editable-text .rendered-markdown')
        );
        return strpos($rendered_html->getText(), 'Neue Information!') !== false;
    });
    take_pageshot($driver, 'startseite_banner_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('startseite', 'startseite');
}

function test_startseite_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text .olz-edit-button')
    );
    click($edit_button);
    sleep(1);

    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-snippet-modal #text-input')
    );
    sendKeys($text_input, 'Neue Information!');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-snippet-modal #images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-snippet-modal #files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#edit-snippet-modal #files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'startseite_banner_edit');
}
