<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/timing.php';

function reset_dev_data() {
    global $base_url;
    tick('reset');
    for ($i = 0; $i < 100; $i++) {
        $result = file_get_contents("{$base_url}/api/executeCommand?access_token=public_dev_data_access_token&request={\"command\":\"olz:db-reset\",\"argv\":\"content\"}");
        $output = json_decode($result, true)['output'] ?? null;
        if ($output === "Database content reset successful.\n") {
            tock('reset', 'db_reset');
            return;
        }
        echo "DB content reset failed: {$output}\n";
        usleep(100 * 1000);
    }
    throw new \Exception("Resetting dev data timed out");
}
