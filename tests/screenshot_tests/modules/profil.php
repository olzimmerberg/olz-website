<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$profil_url = '/profil';

function test_profil(RemoteWebDriver $driver, string $base_url): void {
    global $profil_url;
    tick('profil');

    test_profil_readonly($driver, $base_url);

    tock('profil', 'profil');
}

function test_profil_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $profil_url;
    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$profil_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$profil_url}");
    take_pageshot($driver, 'profil_admin');

    $change_password_button_elem = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-button')
    );
    click($change_password_button_elem);
    $driver->wait()->until(function () use ($driver) {
        $change_password_modal = $driver->findElement(
            WebDriverBy::cssSelector('#change-password-modal')
        );
        return $change_password_modal->getCssValue('opacity') == 1;
    });

    $old_password_input = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-modal #oldPassword-input')
    );
    sendKeys($old_password_input, 'kurz');
    $new_password_input = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-modal #newPassword-input')
    );
    sendKeys($new_password_input, 'zukurz');
    $password_repeat_input = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-modal #newPasswordRepeat-input')
    );
    sendKeys($password_repeat_input, 'anders');
    $submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#change-password-modal #submit-button')
    );
    click($submit_button);
    take_pageshot($driver, 'change_password_admin');

    logout($driver, $base_url);
}
