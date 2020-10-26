<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';

function test_startseite($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');
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
