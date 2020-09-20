<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/?page=1';
$live_file_path = './dev-server/results/_live.json';

function test_live_results($driver, $base_url) {
    global $live_file_path, $startseite_url;
    $live_file_content = json_encode([
        'last_updated_at' => date('Y-m-d H:i:s'),
        'file' => 'results.xml',
    ]);
    file_put_contents($live_file_path, $live_file_content);

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    take_pageshot($driver, 'live_results_link');

    unlink($live_file_path);
}
