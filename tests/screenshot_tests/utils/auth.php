<?php

namespace Facebook\WebDriver;

$login_api_url = '/api/index.php/login';
$logout_api_url = '/api/index.php/logout';

function login($driver, $base_url, $username, $password) {
    global $login_api_url;
    $esc_username = json_encode($username);
    $esc_password = json_encode($password);
    $get_params = "?username={$esc_username}&password={$esc_password}";
    $driver->get("{$base_url}{$login_api_url}{$get_params}");
    $driver->navigate()->refresh();
}

function logout($driver, $base_url) {
    global $logout_api_url;
    $driver->get("{$base_url}{$logout_api_url}");
    $driver->navigate()->refresh();
}
