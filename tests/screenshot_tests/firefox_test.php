<?php

// An example of using php-webdriver.
// Do not forget to run composer install before. You must also have Selenium server started and listening on port 4444.

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../../vendor/autoload.php';

require_once __DIR__.'/utils/window.php';

require_once __DIR__.'/modules/startseite.php';
require_once __DIR__.'/modules/aktuell.php';
require_once __DIR__.'/modules/leistungssport.php';
require_once __DIR__.'/modules/termine.php';
require_once __DIR__.'/modules/galerie.php';
require_once __DIR__.'/modules/forum.php';
require_once __DIR__.'/modules/karten.php';
require_once __DIR__.'/modules/material.php';
require_once __DIR__.'/modules/service.php';
require_once __DIR__.'/modules/newsletter.php';
require_once __DIR__.'/modules/kontakt.php';
require_once __DIR__.'/modules/trophy.php';
require_once __DIR__.'/modules/error.php';
require_once __DIR__.'/modules/search.php';
require_once __DIR__.'/modules/fuer_einsteiger.php';
require_once __DIR__.'/modules/datenschutz.php';
require_once __DIR__.'/modules/login_logout.php';
require_once __DIR__.'/modules/profil.php';
require_once __DIR__.'/modules/divmail.php';
require_once __DIR__.'/modules/webftp.php';
require_once __DIR__.'/modules/live_results.php';
require_once __DIR__.'/modules/resultate.php';

date_default_timezone_set('Europe/Zurich');

// For Selenium 4, Chromedriver or Geckodriver, use http://localhost:4444/
$host = 'http://localhost:4444/';

$capabilities = DesiredCapabilities::firefox();

$driver = RemoteWebDriver::create($host, $capabilities);

$base_url = 'http://127.0.0.1:30270/_/';

try {
    set_window_size($driver, 1280, 1024);

    test_startseite($driver, $base_url);
    test_aktuell($driver, $base_url);
    test_leistungssport($driver, $base_url);
    test_termine($driver, $base_url);
    test_galerie($driver, $base_url);
    test_forum($driver, $base_url);
    test_karten($driver, $base_url);
    test_material($driver, $base_url);
    test_service($driver, $base_url);
    test_newsletter($driver, $base_url);
    test_kontakt($driver, $base_url);
    test_trophy($driver, $base_url);
    test_error($driver, $base_url);
    test_search($driver, $base_url);
    test_fuer_einsteiger($driver, $base_url);
    test_datenschutz($driver, $base_url);
    test_login_logout($driver, $base_url);
    test_profil($driver, $base_url);
    test_divmail($driver, $base_url);
    test_webftp($driver, $base_url);
    test_live_results($driver, $base_url);
    test_resultate($driver, $base_url);

    // $driver->get('http://127.0.0.1:30270/_/');
    // $login_menu = $driver->findElement(WebDriverBy::id('menu_a_page10'));
    // echo "Login text: {$login_menu->getText()}";
    // $login_menu->click();
    // $driver->wait()->until(
    //     WebDriverExpectedCondition::elementToBeClickable(
    //         WebDriverBy::cssSelector('input[name="username"]')
    //     )
    // );
    // $driver->findElement(WebDriverBy::cssSelector('input[name="username"]'))
    //     ->sendKeys('admin');
    // $driver->findElement(WebDriverBy::cssSelector('input[name="passwort"]'))
    //     ->sendKeys('adm1n');
    // $driver->findElement(WebDriverBy::cssSelector('input[value="Login"]'))
    //     ->click();
    //
    // $driver->wait()->until(
    //     WebDriverExpectedCondition::elementToBeClickable(
    //         WebDriverBy::id('menu_a_pageftp')
    //     )
    // );
    // $driver->findElement(WebDriverBy::id('menu_a_pageftp'))->click();
    // $driver->wait()->until(
    //     WebDriverExpectedCondition::elementToBeClickable(
    //         WebDriverBy::id('content_double')
    //     )
    // );
    // $ftp_elem = $driver->findElement(WebDriverBy::id('content_double'));
    // echo "Login text: {$ftp_elem->getText()}";

    //
    // // write 'PHP' in the search box
    // $driver->findElement(WebDriverBy::id('searchInput')) // find search input element
    //     ->sendKeys('PHP') // fill the search box
    //     ->submit(); // submit the whole form
    //
    // // wait until 'PHP' is shown in the page heading element
    // $driver->wait()->until(
    //     WebDriverExpectedCondition::elementTextContains(WebDriverBy::id('firstHeading'), 'PHP')
    // );
    //
    // // print title of the current page to output
    // echo "The title is '" . $driver->getTitle() . "'\n";
    //
    // // print URL of current page to output
    // echo "The current URL is '" . $driver->getCurrentURL() . "'\n";
    //
    // // find element of 'History' item in menu
    // $historyButton = $driver->findElement(
    //     WebDriverBy::cssSelector('#ca-history a')
    // );
    //
    // // read text of the element and print it to output
    // echo "About to click to button with text: '" . $historyButton->getText() . "'\n";
    //
    // // click the element to navigate to revision history page
    // $historyButton->click();
    //
    // // wait until the target page is loaded
    // $driver->wait()->until(
    //     WebDriverExpectedCondition::titleContains('Revision history')
    // );
    //
    // // print the title of the current page
    // echo "The title is '" . $driver->getTitle() . "'\n";
    //
    // // print the URI of the current page
    //
    // echo "The current URI is '" . $driver->getCurrentURL() . "'\n";
    //
    // // delete all cookies
    // $driver->manage()->deleteAllCookies();
    //
    // // add new cookie
    // $cookie = new Cookie('cookie_set_by_selenium', 'cookie_value');
    // $driver->manage()->addCookie($cookie);
    //
    // // dump current cookies to output
    // $cookies = $driver->manage()->getCookies();
    // print_r($cookies);
} finally {
    $driver->quit();
}
