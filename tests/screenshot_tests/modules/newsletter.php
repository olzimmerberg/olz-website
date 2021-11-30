<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service.php';
$name = 'Test, User';
$email = 'newsletter-test@olzimmerberg.ch';

function test_newsletter($driver, $base_url) {
    global $service_url, $name, $email;
    tick('newsletter');

    login($driver, $base_url, 'vorstand', 'v0r57and');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    take_pageshot($driver, 'newsletter_vorstand');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    take_pageshot($driver, 'newsletter_original');

    $telegram_monthly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="monthly-preview"]')
    );
    $telegram_monthly_preview_elem->click();
    $telegram_weekly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-preview"]')
    );
    $telegram_weekly_preview_elem->click();
    $telegram_deadline_warning_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="deadline-warning"]')
    );
    $telegram_deadline_warning_elem->click();
    $telegram_deadline_warning_days_elem = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form select[name="deadline-warning-days"]')
    ));
    $telegram_deadline_warning_days_elem->selectByVisibleText('2');
    $telegram_daily_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary"]')
    );
    $telegram_daily_summary_elem->click();
    $telegram_daily_summary_aktuell_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary-aktuell"]')
    );
    $telegram_daily_summary_aktuell_elem->click();
    $telegram_daily_summary_blog_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary-blog"]')
    );
    $telegram_daily_summary_blog_elem->click();
    $telegram_weekly_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary"]')
    );
    $telegram_weekly_summary_elem->click();
    $telegram_weekly_summary_forum_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary-forum"]')
    );
    $telegram_weekly_summary_forum_elem->click();
    $telegram_weekly_summary_galerie_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary-galerie"]')
    );
    $telegram_weekly_summary_galerie_elem->click();
    $telegram_submit_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-submit')
    );
    $telegram_submit_elem->click();

    $email_monthly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="monthly-preview"]')
    );
    $email_monthly_preview_elem->click();
    $email_weekly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-preview"]')
    );
    $email_weekly_preview_elem->click();
    $email_deadline_warning_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="deadline-warning"]')
    );
    $email_deadline_warning_elem->click();
    $email_deadline_warning_days_elem = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form select[name="deadline-warning-days"]')
    ));
    $email_deadline_warning_days_elem->selectByVisibleText('2');
    $email_daily_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary"]')
    );
    $email_daily_summary_elem->click();
    $email_daily_summary_aktuell_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary-aktuell"]')
    );
    $email_daily_summary_aktuell_elem->click();
    $email_daily_summary_blog_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary-blog"]')
    );
    $email_daily_summary_blog_elem->click();
    $email_weekly_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary"]')
    );
    $email_weekly_summary_elem->click();
    $email_weekly_summary_forum_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary-forum"]')
    );
    $email_weekly_summary_forum_elem->click();
    $email_weekly_summary_galerie_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary-galerie"]')
    );
    $email_weekly_summary_galerie_elem->click();
    $email_submit_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-submit')
    );
    $email_submit_elem->click();
    take_pageshot($driver, 'newsletter_modified');

    logout($driver, $base_url);

    reset_dev_data();
    tock('newsletter', 'newsletter');
}
