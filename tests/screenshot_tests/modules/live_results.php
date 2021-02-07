<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../../../src/config/date.php';
require_once __DIR__.'/../utils/screenshot.php';

$startseite_url = '/startseite.php';
$live_file_path = './dev-server/results/_live.json';

function test_live_results($driver, $base_url) {
    global $live_file_path, $startseite_url;
    tick('live_results');

    test_live_results_readonly($driver, $base_url);

    tock('live_results', 'live_results');
}

function test_live_results_readonly($driver, $base_url) {
    global $live_file_path, $startseite_url;
    $live_file_content = json_encode([
        'last_updated_at' => olz_current_date('Y-m-d H:i:s'),
        'file' => 'results.xml',
    ]);
    file_put_contents($live_file_path, $live_file_content);

    $driver->get("{$base_url}{$startseite_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$startseite_url}");
    take_pageshot($driver, 'live_results_link');

    unlink($live_file_path);
}
