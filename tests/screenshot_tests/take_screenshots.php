<?php

// An example of using php-webdriver.
// Do not forget to run composer install before. You must also have Selenium server started and listening on port 4444.

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../../vendor/autoload.php';

require_once __DIR__.'/utils/database.php';
require_once __DIR__.'/utils/screenshot.php';
require_once __DIR__.'/utils/timing.php';
require_once __DIR__.'/utils/window.php';

require_once __DIR__.'/modules/news.php';
require_once __DIR__.'/modules/app_files.php';
require_once __DIR__.'/modules/apps.php';
require_once __DIR__.'/modules/weekly_picture.php';
require_once __DIR__.'/modules/datenschutz.php';
require_once __DIR__.'/modules/downloads.php';
require_once __DIR__.'/modules/email_reaktion.php';
require_once __DIR__.'/modules/error.php';
require_once __DIR__.'/modules/fragen_und_antworten.php';
require_once __DIR__.'/modules/fuer_einsteiger.php';
require_once __DIR__.'/modules/karten.php';
require_once __DIR__.'/modules/konto_passwort.php';
require_once __DIR__.'/modules/links.php';
require_once __DIR__.'/modules/live_results.php';
require_once __DIR__.'/modules/login_logout.php';
require_once __DIR__.'/modules/material.php';
require_once __DIR__.'/modules/newsletter.php';
require_once __DIR__.'/modules/profil.php';
require_once __DIR__.'/modules/resultate.php';
require_once __DIR__.'/modules/suche.php';
require_once __DIR__.'/modules/service.php';
require_once __DIR__.'/modules/sitemap.php';
require_once __DIR__.'/modules/startseite.php';
require_once __DIR__.'/modules/termine.php';
require_once __DIR__.'/modules/trophy.php';
require_once __DIR__.'/modules/verein.php';
require_once __DIR__.'/modules/webdav.php';
require_once __DIR__.'/modules/zimmerberg_ol.php';

date_default_timezone_set('Europe/Zurich');

$host = 'http://localhost:4444/';

$browser = $argv[1];
$block_to_run = $argv[2] ?? '';

if ($browser == 'firefox') {
    $capabilities = DesiredCapabilities::firefox();
} elseif ($browser == 'chrome') {
    $capabilities = DesiredCapabilities::chrome();
} else {
    exit("Invalid browser: {$browser}");
}

$driver = RemoteWebDriver::create($host, $capabilities);

$base_url = 'http://127.0.0.1:30270';

function init_test_block($driver) {
    reset_timing();

    set_window_size($driver, 1280, 1024);
}

$blocks = [
    function ($driver, $base_url) {
        init_test_block($driver);

        // no specific order
        test_news($driver, $base_url);
        test_sitemap($driver, $base_url);

        echo get_pretty_timing_report();
    },
    function ($driver, $base_url) {
        init_test_block($driver);

        // no specific order
        test_startseite($driver, $base_url);
        test_karten($driver, $base_url);
        test_material($driver, $base_url);
        test_links($driver, $base_url);
        test_downloads($driver, $base_url);
        test_email_reaktion($driver, $base_url);
        test_verein($driver, $base_url);
        test_trophy($driver, $base_url);
        test_error($driver, $base_url);
        test_suche($driver, $base_url);
        test_zimmerberg_ol($driver, $base_url);
        test_apps($driver, $base_url);

        echo get_pretty_timing_report();
    },
    function ($driver, $base_url) {
        init_test_block($driver);

        // no specific order
        test_termine($driver, $base_url);
        test_konto_passwort($driver, $base_url);
        test_app_files($driver, $base_url);
        test_fuer_einsteiger($driver, $base_url);
        test_fragen_und_antworten($driver, $base_url);
        test_datenschutz($driver, $base_url);
        test_webdav($driver, $base_url);
        test_login_logout($driver, $base_url);
        test_profil($driver, $base_url);
        test_live_results($driver, $base_url);
        test_resultate($driver, $base_url);
        test_service($driver, $base_url);
        test_newsletter($driver, $base_url);
        test_weekly_picture($driver, $base_url);

        echo get_pretty_timing_report();
    },
];

try {
    for ($block = 0; $block < count($blocks); $block++) {
        if ($block_to_run == $block || $block_to_run == '') {
            $function = $blocks[$block];
            $function($driver, $base_url);
        }
    }
} catch (\Exception $e) {
    take_pageshot($driver, '_error_screenshot');
    throw $e;
} finally {
    $driver->quit();
}
