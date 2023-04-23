<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$startseite_url = '/';

function test_startseite($driver, $base_url) {
    global $startseite_url;
    tick('startseite');

    test_startseite_readonly($driver, $base_url);

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text .olz-edit-submit')
    );
    click($save_button);
    $driver->wait()->until(function () use ($driver) {
        $rendered_html = $driver->findElement(
            WebDriverBy::cssSelector('#important-banner .olz-editable-text .rendered-html')
        );
        return strpos($rendered_html->getText(), 'Neue Information!') !== false;
    });
    take_pageshot($driver, 'startseite_banner_finished');

    logout($driver, $base_url);

    reset_dev_data();
    tock('startseite', 'startseite');
}

function test_startseite_readonly($driver, $base_url) {
    global $startseite_url;
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'startseite');

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text .olz-edit-button')
    );
    click($edit_button);
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#important-banner .olz-editable-text textarea[name="text"]')
    );
    sendKeys($text_input, 'Neue Information!');
    take_pageshot($driver, 'startseite_banner_edit');
}
