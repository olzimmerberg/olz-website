<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/timing.php';

$login_api_url = '/api/login';
$logout_api_url = '/api/logout';

function login($driver, $base_url, $username, $password) {
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

function logout($driver, $base_url) {
    global $logout_api_url;
    tick('logout');
    $driver->get("{$base_url}{$logout_api_url}");
    $driver->navigate()->refresh();
    tock('logout', 'logout');
}
