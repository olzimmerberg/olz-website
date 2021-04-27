<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';

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
    $password_input->sendKeys('genügend&gleich');
    $password_repeat_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-repeat-input')
    );
    $password_repeat_input->clear();
    $password_repeat_input->sendKeys('genügend&gleich');
    $email_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-email-input')
    );
    $email_input->sendKeys('@olzimmerberg.ch');
    $birthdate_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-birthdate-input')
    );
    $birthdate_input->clear();
    $birthdate_input->sendKeys('13.1.2006');
    $submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#sign-up-with-password-submit-button')
    );
    $submit_button->click();
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
    $first_name_input->sendKeys('Integration T.');
    $last_name_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-last-name-input')
    );
    $last_name_input->sendKeys('User');
    $username_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-username-input')
    );
    $username_input->click();
    $password_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-input')
    );
    $password_input->sendKeys('zukurz');
    $password_repeat_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-password-repeat-input')
    );
    $password_repeat_input->sendKeys('anders');
    $email_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-email-input')
    );
    $email_input->sendKeys('konto-passwort-test');
    $gender_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-gender-input')
    );
    $gender_input->sendKeys('m');
    $birthdate_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-birthdate-input')
    );
    $birthdate_input->sendKeys('30.2.1999');
    $street_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-street-input')
    );
    $street_input->sendKeys('Zimmerbergstrasse 270');
    $postal_code_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-postal-code-input')
    );
    $postal_code_input->sendKeys('8800');
    $city_input = $driver->findElement(
        WebDriverBy::cssSelector('#profile-city-input')
    );
    $city_input->sendKeys('Thalwil');
    $submit_button = $driver->findElement(
        WebDriverBy::cssSelector('#sign-up-with-password-submit-button')
    );
    $submit_button->click();
    take_pageshot($driver, 'konto_passwort_errors');
}
