<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Olz\Utils\EmailUtils;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$email_reaktion_url = '/email_reaktion';

function test_email_reaktion(RemoteWebDriver $driver, string $base_url): void {
    global $email_reaktion_url;
    tick('email_reaktion');

    test_email_reaktion_readonly($driver, $base_url);

    $email_utils = EmailUtils::fromEnv();
    $token = $email_utils->encryptEmailReactionToken([
        'action' => 'unsubscribe',
        'user' => 1,
        'notification_type' => 'monthly_preview',
    ]);
    $driver->get("{$base_url}{$email_reaktion_url}?token={$token}");
    take_pageshot($driver, 'email_reaktion');

    reset_dev_data();
    tock('email_reaktion', 'email_reaktion');
}

function test_email_reaktion_readonly(RemoteWebDriver $driver, string $base_url): void {
    global $email_reaktion_url;
    $driver->get("{$base_url}{$email_reaktion_url}");
    take_pageshot($driver, 'email_reaktion_no_token');
}
