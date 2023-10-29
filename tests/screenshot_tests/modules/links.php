<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$service_url = '/service';

function test_links($driver, $base_url) {
    global $service_url;
    tick('links');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$service_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$service_url}");

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonlinks-neuer-eintrag')
    );
    click($new_button);
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#linksname')
    );
    sendKeys($name_input, 'OLZ');
    $url_input = $driver->findElement(
        WebDriverBy::cssSelector('#linksurl')
    );
    sendKeys($url_input, 'https://olzimmerberg.ch');
    take_pageshot($driver, 'links_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonlinks-vorschau')
    );
    click($preview_button);
    take_pageshot($driver, 'links_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonlinks-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'links_new_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('links', 'links');
}
