<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$profil_url = '/profil.php';

function test_profil($driver, $base_url) {
    global $profil_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$profil_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$profil_url}");
    take_pageshot($driver, 'profil_admin');

    $change_password_button_elem = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-button')
    );
    $change_password_button_elem->click();
    $driver->wait()->until(function () use ($driver) {
        $change_password_modal = $driver->findElement(
            WebDriverBy::cssSelector('#change-password-modal')
        );
        return $change_password_modal->getCssValue('opacity') == 1;
    });

    take_pageshot($driver, 'change_password_admin');

    logout($driver, $base_url);
}
