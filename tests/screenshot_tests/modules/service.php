<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service.php';

function test_service($driver, $base_url) {
    global $service_url;
    tick('service');

    test_service_readonly($driver, $base_url);

    $telegram_daily_summary = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary"]')
    );
    $telegram_daily_summary->click();
    $telegram_submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-submit')
    );
    $telegram_submit_button->click();
    $email_monthly_preview = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="monthly-preview"]')
    );
    $email_monthly_preview->click();
    $email_submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-submit')
    );
    $email_submit_button->click();
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service_modified');

    tock('service', 'service');
}

function test_service_readonly($driver, $base_url) {
    global $service_url;
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");
    take_pageshot($driver, 'service_authenticated');
}
