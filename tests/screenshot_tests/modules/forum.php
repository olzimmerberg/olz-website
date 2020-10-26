<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$forum_url = '/forum.php';

function test_forum($driver, $base_url) {
    global $forum_url;
    $driver->get("{$base_url}{$forum_url}");
    take_pageshot($driver, 'forum');
}
