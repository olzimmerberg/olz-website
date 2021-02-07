<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';

function test_bild_der_woche($driver, $base_url) {
    global $startseite_url;
    tick('bild_der_woche');

    test_bild_der_woche_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonbild_der_woche-neuer-eintrag')
    );
    $new_button->click();
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#bild_der_wochetext')
    );
    $text_input->sendKeys('Neues Bild der Woche');
    take_pageshot($driver, 'bild_der_woche_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonbild_der_woche-vorschau')
    );
    $preview_button->click();
    take_pageshot($driver, 'bild_der_woche_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonbild_der_woche-speichern')
    );
    $save_button->click();
    take_pageshot($driver, 'bild_der_woche_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('bild_der_woche', 'bild_der_woche');
}

function test_bild_der_woche_readonly($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");
    $bild_der_woche_elem = $driver->findElement(
        WebDriverBy::cssSelector('a[href="/img/bild_der_woche//2/img/001.jpg"]')
    );
    $bild_der_woche_elem->click();
    $driver->wait()->until(function () use ($driver) {
        $bild_der_woche_lightgallery_img = $driver->findElement(
            WebDriverBy::cssSelector('img[src="/img/bild_der_woche//2/img/001.jpg"]')
        );
        return $bild_der_woche_lightgallery_img->getCssValue('opacity') == 1;
    });
    take_screenshot($driver, 'startseite_bild_der_woche');
}
