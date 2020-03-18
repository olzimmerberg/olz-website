<?php

namespace Facebook\WebDriver;

$login_url = '/?page=10';
$logout_url = '/?page=Logout';

function login($driver, $base_url, $username, $password) {
    global $login_url;
    $driver->get("{$base_url}{$login_url}");
    $username_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="username"]')
    );
    $password_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="passwort"]')
    );
    $login_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[value="Login"]')
    );
    $username_elem->sendKeys($username);
    $password_elem->sendKeys($password);
    $login_elem->click();
}

function logout($driver, $base_url) {
    global $logout_url;
    $driver->get("{$base_url}{$logout_url}");
}
