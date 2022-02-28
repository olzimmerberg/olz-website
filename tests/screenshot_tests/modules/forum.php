<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$forum_url = '/forum.php';

function test_forum($driver, $base_url) {
    global $forum_url;
    tick('forum');

    test_forum_readonly($driver, $base_url);

    $new_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-neuer-eintrag')
    );
    click($new_button);
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#forumname')
    );
    sendKeys($title_input, 'Testeintrag');
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#forumname2')
    );
    sendKeys($name_input, 'Test User');
    $email_input = $driver->findElement(
        WebDriverBy::cssSelector('#forumemail')
    );
    sendKeys($email_input, 'forum-test@olzimmerberg.ch');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#forumeintrag')
    );
    sendKeys($text_input, 'Viel Inhalt');
    $code_elem = $driver->findElement(
        WebDriverBy::cssSelector('#forumuid')
    );
    $code = $code_elem->getAttribute("value");
    take_pageshot($driver, 'forum_new_edit');

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-vorschau')
    );
    click($preview_button);
    take_pageshot($driver, 'forum_new_preview');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'forum_new_finished');

    $edit_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-eintrag-bearbeiten')
    );
    click($edit_button);
    $code_input = $driver->findElement(
        WebDriverBy::cssSelector('input[name="uid"]')
    );
    sendKeys($code_input, $code);
    $forward_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-weiter')
    );
    click($forward_button);
    take_pageshot($driver, 'forum_code_edit');

    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#forumeintrag')
    );
    sendKeys($text_input, "\n\nNoch mehr Inhalt!");

    $preview_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-vorschau')
    );
    click($preview_button);

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#buttonforum-speichern')
    );
    click($save_button);
    take_pageshot($driver, 'forum_code_edit_finished');

    reset_dev_data();
    tock('forum', 'forum');
}

function test_forum_readonly($driver, $base_url) {
    global $forum_url;
    $driver->get("{$base_url}{$forum_url}");
    take_pageshot($driver, 'forum');
}
