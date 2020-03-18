<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$forum_url = '/?page=5';

function test_forum($driver, $base_url) {
    global $forum_url;
    $driver->get("{$base_url}{$forum_url}");
    take_pageshot($driver, 'forum');
}
