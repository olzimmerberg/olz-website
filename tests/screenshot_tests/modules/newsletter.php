<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$service_url = '/service.php';
$name = 'Test, User';
$email = 'newsletter-test@olzimmerberg.ch';

function test_newsletter($driver, $base_url) {
    global $service_url, $name, $email;
    $driver->get("{$base_url}{$service_url}");
    $subscribe_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="buttonnewsletter"][value="Anmelden"]')
    );
    $subscribe_elem->click();
    $name_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="newslettername"]')
    );
    $name_elem->sendKeys($name);
    $email_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="newsletteremail"]')
    );
    $email_elem->sendKeys($email);
    $forum_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="newsletterkategorie[]"][value="forum"]')
    );
    $forum_elem->click();
    $code_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="newsletteruid"]')
    );
    $code = $code_elem->getAttribute("value");
    take_pageshot($driver, 'newsletter_form');

    $preview_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="buttonnewsletter"][value="Vorschau"]')
    );
    $preview_elem->click();
    take_pageshot($driver, 'newsletter_preview');

    $save_elem = $driver->findElement(
        WebDriverBy::cssSelector('input[name="buttonnewsletter"][value="Speichern"]')
    );
    $save_elem->click();
    take_pageshot($driver, 'newsletter_saved');
}
