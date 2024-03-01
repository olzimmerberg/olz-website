<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$karten_url = '/karten';

function test_karten($driver, $base_url) {
    global $karten_url;
    tick('karten');

    test_karten_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$karten_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$karten_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-karte-button')
    );
    click($new_button);
    sleep(1);
    $position_input = $driver->findElement(
        WebDriverBy::cssSelector('#position-input')
    );
    sendKeys($position_input, '3');
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#name-input')
    );
    sendKeys($name_input, 'Die Karte');
    $center_x_input = $driver->findElement(
        WebDriverBy::cssSelector('#centerX-input')
    );
    sendKeys($center_x_input, '735550');
    $center_y_input = $driver->findElement(
        WebDriverBy::cssSelector('#centerY-input')
    );
    sendKeys($center_y_input, '188600');
    $year_input = $driver->findElement(
        WebDriverBy::cssSelector('#year-input')
    );
    sendKeys($year_input, '2020');
    $scale_input = $driver->findElement(
        WebDriverBy::cssSelector('#scale-input')
    );
    sendKeys($scale_input, '1:15\'000');
    $kind_scool_input = $driver->findElement(
        WebDriverBy::cssSelector('#isKindScool-input')
    );
    click($kind_scool_input);
    $place_input = $driver->findElement(
        WebDriverBy::cssSelector('#place-input')
    );
    sendKeys($place_input, 'Wuut');
    $zoom_input = $driver->findElement(
        WebDriverBy::cssSelector('#zoom-input')
    );
    sendKeys($zoom_input, '2');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    take_pageshot($driver, 'karten_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'karten_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('karten', 'karten');
}

function test_karten_readonly($driver, $base_url) {
    global $karten_url;
    $driver->get("{$base_url}{$karten_url}");
    take_pageshot($driver, 'karten');
}
