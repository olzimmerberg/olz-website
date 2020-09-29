<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$profile_url = '/?page=100';

function test_profile($driver, $base_url) {
    global $profile_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$profile_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$profile_url}");
    take_pageshot($driver, 'profile_admin');

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
    sleep(0.2);

    take_pageshot($driver, 'change_password_admin');

    logout($driver, $base_url);
}
