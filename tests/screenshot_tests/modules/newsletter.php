<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

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
    click($telegram_monthly_preview_elem);
    $telegram_weekly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-preview"]')
    );
    click($telegram_weekly_preview_elem);
    $telegram_deadline_warning_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="deadline-warning"]')
    );
    click($telegram_deadline_warning_elem);
    $telegram_deadline_warning_days_elem = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form select[name="deadline-warning-days"]')
    ));
    $telegram_deadline_warning_days_elem->selectByVisibleText('2');
    $telegram_daily_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary"]')
    );
    click($telegram_daily_summary_elem);
    $telegram_daily_summary_aktuell_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary-aktuell"]')
    );
    click($telegram_daily_summary_aktuell_elem);
    $telegram_daily_summary_blog_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="daily-summary-blog"]')
    );
    click($telegram_daily_summary_blog_elem);
    $telegram_weekly_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary"]')
    );
    click($telegram_weekly_summary_elem);
    $telegram_weekly_summary_forum_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary-forum"]')
    );
    click($telegram_weekly_summary_forum_elem);
    $telegram_weekly_summary_galerie_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary-galerie"]')
    );
    click($telegram_weekly_summary_galerie_elem);
    $telegram_weekly_summary_termine_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-form input[name="weekly-summary-termine"]')
    );
    click($telegram_weekly_summary_termine_elem);
    $telegram_submit_elem = $driver->findElement(
        WebDriverBy::cssSelector('#telegram-notifications-submit')
    );
    click($telegram_submit_elem);

    $email_monthly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="monthly-preview"]')
    );
    click($email_monthly_preview_elem);
    $email_weekly_preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-preview"]')
    );
    click($email_weekly_preview_elem);
    $email_deadline_warning_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="deadline-warning"]')
    );
    click($email_deadline_warning_elem);
    $email_deadline_warning_days_elem = new WebDriverSelect($driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form select[name="deadline-warning-days"]')
    ));
    $email_deadline_warning_days_elem->selectByVisibleText('2');
    $email_daily_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary"]')
    );
    click($email_daily_summary_elem);
    $email_daily_summary_aktuell_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary-aktuell"]')
    );
    click($email_daily_summary_aktuell_elem);
    $email_daily_summary_blog_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="daily-summary-blog"]')
    );
    click($email_daily_summary_blog_elem);
    $email_weekly_summary_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary"]')
    );
    click($email_weekly_summary_elem);
    $email_weekly_summary_forum_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary-forum"]')
    );
    click($email_weekly_summary_forum_elem);
    $email_weekly_summary_galerie_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary-galerie"]')
    );
    click($email_weekly_summary_galerie_elem);
    $email_weekly_summary_termine_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-form input[name="weekly-summary-termine"]')
    );
    click($email_weekly_summary_termine_elem);
    $email_submit_elem = $driver->findElement(
        WebDriverBy::cssSelector('#email-notifications-submit')
    );
    click($email_submit_elem);
    take_pageshot($driver, 'newsletter_modified');

    logout($driver, $base_url);

    reset_dev_data();
    tock('newsletter', 'newsletter');
}
