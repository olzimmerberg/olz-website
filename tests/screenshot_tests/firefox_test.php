<?php

// An example of using php-webdriver.
// Do not forget to run composer install before. You must also have Selenium server started and listening on port 4444.

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../../vendor/autoload.php';

require_once __DIR__.'/utils/database.php';
require_once __DIR__.'/utils/timing.php';
require_once __DIR__.'/utils/window.php';

require_once __DIR__.'/modules/index.php';
require_once __DIR__.'/modules/startseite.php';
require_once __DIR__.'/modules/bild_der_woche.php';
require_once __DIR__.'/modules/aktuell.php';
require_once __DIR__.'/modules/leistungssport.php';
require_once __DIR__.'/modules/termine.php';
require_once __DIR__.'/modules/galerie.php';
require_once __DIR__.'/modules/forum.php';
require_once __DIR__.'/modules/karten.php';
require_once __DIR__.'/modules/material.php';
require_once __DIR__.'/modules/service.php';
require_once __DIR__.'/modules/links.php';
require_once __DIR__.'/modules/downloads.php';
require_once __DIR__.'/modules/newsletter.php';
require_once __DIR__.'/modules/email_reaktion.php';
require_once __DIR__.'/modules/kontakt.php';
require_once __DIR__.'/modules/trophy.php';
require_once __DIR__.'/modules/error.php';
require_once __DIR__.'/modules/search.php';
require_once __DIR__.'/modules/fragen_und_antworten.php';
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

$base_url = 'http://127.0.0.1:30270/';
$code_href = "{$base_url}_/";

function init_test_block($driver) {
    reset_dev_data();

    reset_timing();

    set_window_size($driver, 1280, 1024);
}

$blocks = [
    function ($driver, $code_href) {
        init_test_block($driver);

        test_index($driver, $code_href);
        test_startseite($driver, $code_href);
        test_bild_der_woche($driver, $code_href);
        test_aktuell($driver, $code_href);
        test_leistungssport($driver, $code_href);
        test_termine($driver, $code_href);
        test_galerie($driver, $code_href);
        test_forum($driver, $code_href);

        echo get_pretty_timing_report();
    },
    function ($driver, $code_href) {
        init_test_block($driver);

        test_karten($driver, $code_href);
        test_material($driver, $code_href);
        test_service($driver, $code_href);
        test_links($driver, $code_href);
        test_downloads($driver, $code_href);
        test_newsletter($driver, $code_href);
        test_email_reaktion($driver, $code_href);
        test_kontakt($driver, $code_href);
        test_trophy($driver, $code_href);
        test_error($driver, $code_href);
        test_search($driver, $code_href);

        echo get_pretty_timing_report();
    },
    function ($driver, $code_href) {
        init_test_block($driver);

        test_fuer_einsteiger($driver, $code_href);
        test_fragen_und_antworten($driver, $code_href);
        test_datenschutz($driver, $code_href);
        test_login_logout($driver, $code_href);
        test_profil($driver, $code_href);
        test_divmail($driver, $code_href);
        test_webftp($driver, $code_href);
        test_live_results($driver, $code_href);
        test_resultate($driver, $code_href);

        echo get_pretty_timing_report();
    },
];

$block_to_run = $argv[1] ?? '';

try {
    for ($block = 0; $block < count($blocks); $block++) {
        if ($block_to_run == $block || $block_to_run == '') {
            $function = $blocks[$block];
            $function($driver, $code_href);
        }
    }

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
