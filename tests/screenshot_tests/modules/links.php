<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$service_url = '/service';

function test_links(RemoteWebDriver $driver, string $base_url): void {
    global $service_url;
    tick('links');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-link-button')
    );
    click($new_button);

    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-link-modal #name-input')
    );
    sendKeys($name_input, 'OLZ');

    $position_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-link-modal #position-input')
    );
    sendKeys($position_input, '0');

    $url_input = $driver->findElement(
        WebDriverBy::cssSelector('#edit-link-modal #url-input')
    );
    sendKeys($url_input, 'https://olzimmerberg.ch');

    take_pageshot($driver, 'links_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    take_pageshot($driver, 'links_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('links', 'links');
}
