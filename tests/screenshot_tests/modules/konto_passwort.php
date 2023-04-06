<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$konto_passwort_url = '/konto_passwort.php';
$webftp_url = '/webftp.php';

function test_konto_passwort($driver, $base_url) {
    global $konto_passwort_url, $webftp_url;
    tick('konto_passwort');

    test_konto_passwort_readonly($driver, $base_url);

    $password_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-input')
    );
    $password_input->clear();
    sendKeys($password_input, 'genügend&gleich');
    $password_repeat_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-repeat-input')
    );
    $password_repeat_input->clear();
    sendKeys($password_repeat_input, 'genügend&gleich');
    $email_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-email-input')
    );
    sendKeys($email_input, '@staging.olzimmerberg.ch');
    $birthdate_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-birthdate-input')
    );
    $birthdate_input->clear();
    sendKeys($birthdate_input, '13.1.2006');
    $recaptcha_consent_input = $driver->findElement(
        WebDriverBy::cssSelector('input[name="recaptcha-consent-given"]')
    );
    click($recaptcha_consent_input);
    sleep(random_int(2, 6));
    usleep(random_int(0, 999999));
    $cookie_consent_input = $driver->findElement(
        WebDriverBy::cssSelector('input[name="cookie-consent-given"]')
    );
    click($cookie_consent_input);
    sleep(random_int(1, 2));
    usleep(random_int(0, 999999));
    $submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#sign-up-with-password-submit-button')
    );
    click($submit_button);
    sleep(1);
    take_pageshot($driver, 'konto_passwort_submitted');

    $driver->get("{$base_url}{$webftp_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$webftp_url}");
    take_pageshot($driver, 'konto_passwort_new_webftp');

    logout($driver, $base_url);

    reset_dev_data();
    tock('konto_passwort', 'konto_passwort');
}

function test_konto_passwort_readonly($driver, $base_url) {
    global $konto_passwort_url;
    $driver->get("{$base_url}{$konto_passwort_url}");

    $first_name_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-first-name-input')
    );
    sendKeys($first_name_input, 'Integration T.');
    $last_name_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-last-name-input')
    );
    sendKeys($last_name_input, 'User');
    $username_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-username-input')
    );
    click($username_input);
    $password_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-input')
    );
    sendKeys($password_input, 'zukurz');
    $password_repeat_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-repeat-input')
    );
    sendKeys($password_repeat_input, 'anders');
    $email_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-email-input')
    );
    sendKeys($email_input, 'konto-passwort-test');
    $gender_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-gender-input')
    );
    sendKeys($gender_input, 'm');
    $birthdate_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-birthdate-input')
    );
    sendKeys($birthdate_input, '30.2.1999');
    $street_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-street-input')
    );
    sendKeys($street_input, 'Zimmerbergstrasse 270');
    $postal_code_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-postal-code-input')
    );
    sendKeys($postal_code_input, '8800');
    $city_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-city-input')
    );
    sendKeys($city_input, 'Thalwil');
    $submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#sign-up-with-password-submit-button')
    );
    click($submit_button);
    take_pageshot($driver, 'konto_passwort_errors');

    $hide_tooltips_script = <<<'ZZZZZZZZZZ'
    [...document.querySelectorAll('.tooltip')].map(elem => {
        elem.style.display = 'none';
    });
    ZZZZZZZZZZ;
    $driver->executeScript($hide_tooltips_script);
}
