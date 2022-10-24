<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$startseite_url = '/startseite.php';

function test_weekly_picture($driver, $base_url) {
    global $startseite_url;
    tick('weekly_picture');

    test_weekly_picture_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-weekly-picture-button')
    );
    click($new_button);
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#weekly-picture-text-input')
    );
    sendKeys($text_input, 'Neues Bild der Woche');
    take_pageshot($driver, 'weekly_picture_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    take_pageshot($driver, 'weekly_picture_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('weekly_picture', 'weekly_picture');
}

function test_weekly_picture_readonly($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");
    $weekly_picture_elem = $driver->findElement(
        WebDriverBy::cssSelector('a[href="/img/weekly_picture//2/img/001.jpg"]')
    );
    click($weekly_picture_elem);
    $driver->wait()->until(function () use ($driver) {
        $weekly_picture_lightgallery_img = $driver->findElement(
            WebDriverBy::cssSelector('img[src="/img/weekly_picture//2/img/001.jpg"]')
        );
        return $weekly_picture_lightgallery_img->getCssValue('opacity') == 1;
    });
    take_pageshot($driver, 'startseite_weekly_picture');
}
