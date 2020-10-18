<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/auth.php';
require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/?page=1';
$username = 'admin';
$password = 'adm1n';

function test_login_logout($driver, $base_url) {
    global $startseite_url, $username, $password;

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    $account_menu_elem->click();

    $login_menu_item_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-menu-item')
    );
    $login_menu_item_elem->click();
    $driver->wait()->until(function () use ($driver) {
        $login_modal = $driver->findElement(
            WebDriverBy::cssSelector('#login-modal')
        );
        return $login_modal->getCssValue('opacity') == 1;
    });
    sleep(0.5);

    take_pageshot($driver, 'login_modal');

    $username_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-username-input')
    );
    $username_elem->sendKeys($username);
    $password_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-password-input')
    );
    $password_elem->sendKeys($password);
    $login_button_elem = $driver->findElement(
        WebDriverBy::cssSelector('#login-button')
    );
    $login_button_elem->click();

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    $account_menu_elem->click();
    take_pageshot($driver, 'logout_account_menu');

    $logout_menu_item_elem = $driver->findElement(
        WebDriverBy::cssSelector('#logout-menu-item')
    );
    $logout_menu_item_elem->click();

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $account_menu_elem = $driver->findElement(
        WebDriverBy::cssSelector('#account-menu-link')
    );
    $account_menu_elem->click();
    take_pageshot($driver, 'login_account_menu');
}
