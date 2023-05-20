<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$startseite_url = '/';
$username = 'admin';
$password = 'adm1n';

function test_login_logout($driver, $base_url) {
    global $startseite_url, $username, $password;
    tick('login_logout');

    test_login_logout_readonly($driver, $base_url);

    tock('login_logout', 'login_logout');
}

// This is not actually readonly, as `AuthRequest`s will be created, but it does not change the looks of the interface.
function test_login_logout_readonly($driver, $base_url) {
    global $startseite_url, $username, $password;

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    click($account_menu_elem);

    $login_menu_item_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-menu-item')
    );
    click($login_menu_item_elem);
    $driver->wait()->until(function () use ($driver) {
        $login_modal = $driver->findElement(
            WebDriverBy::cssSelector('#login-modal')
        );
        return $login_modal->getCssValue('opacity') == 1;
    });
    usleep(100 * 1000);

    take_pageshot($driver, 'login_modal');

    $username_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-username-input')
    );
    sendKeys($username_elem, $username);
    $password_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-password-input')
    );
    sendKeys($password_elem, $password);
    $login_button_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-button')
    );
    click($login_button_elem);
    sleep(1);

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    click($account_menu_elem);
    take_pageshot($driver, 'logout_account_menu');

    $logout_menu_item_elem = $driver->findElement(
        WebDriverBy::cssSelector('#logout-menu-item')
    );
    click($logout_menu_item_elem);

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    click($account_menu_elem);
    take_pageshot($driver, 'login_account_menu');
}
