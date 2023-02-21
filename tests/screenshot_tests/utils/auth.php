<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/timing.php';

$login_api_url = '/api/login';
$logout_api_url = '/api/logout';

function login($driver, $base_url, $username, $password) {
    global $login_api_url;
    tick('login');
    $esc_username = json_encode($username);
    $esc_password = json_encode($password);
    $get_params = "?usernameOrEmail={$esc_username}".
        "&password={$esc_password}&rememberMe=false";
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
