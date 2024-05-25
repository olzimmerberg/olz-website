<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/timing.php';

$login_api_url = '/api/login';
$logout_api_url = '/api/logout';

function login(RemoteWebDriver $driver, string $base_url, string $username, string $password): void {
    global $login_api_url;
    tick('login');
    $esc_request = json_encode([
        'usernameOrEmail' => $username,
        'password' => $password,
        'rememberMe' => false,
    ]);
    $get_params = "?request={$esc_request}";
    $driver->get("{$base_url}{$login_api_url}{$get_params}");
    $driver->navigate()->refresh();
    tock('login', 'login');
}

function logout(RemoteWebDriver $driver, string $base_url): void {
    global $logout_api_url;
    tick('logout');
    $driver->get("{$base_url}{$logout_api_url}");
    $driver->navigate()->refresh();
    tock('logout', 'logout');
}
