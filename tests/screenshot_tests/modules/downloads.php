<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$service_url = '/service';

function test_downloads($driver, $base_url) {
    global $service_url;
    tick('downloads');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-download-button')
    );
    click($new_button);
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#download-name-input')
    );
    sendKeys($name_input, 'Neues Jahresprogramm');

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#download-file-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#download-file-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'downloads_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'downloads_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('downloads', 'downloads');
}
