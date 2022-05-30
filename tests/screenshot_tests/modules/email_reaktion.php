<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../../../_/utils/notify/EmailUtils.php';
require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$email_reaktion_url = '/email_reaktion.php';

function test_email_reaktion($driver, $base_url) {
    global $email_reaktion_url;
    tick('email_reaktion');

    test_email_reaktion_readonly($driver, $base_url);

    $email_utils = \EmailUtils::fromEnv();
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

function test_email_reaktion_readonly($driver, $base_url) {
    global $email_reaktion_url;
    $driver->get("{$base_url}{$email_reaktion_url}");
    take_pageshot($driver, 'email_reaktion_no_token');
}
