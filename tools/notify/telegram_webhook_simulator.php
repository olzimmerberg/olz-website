<?php

// Server Configuration
global $_CONFIG;
require_once __DIR__.'/../../src/config/server.php';

$telegram_api_url = 'https://api.telegram.org/bot'.$_CONFIG->getTelegramBotToken().'/';
$authenticity_code = $_CONFIG->getTelegramAuthenticityCode();

$server_domain = $argv[1];
$simulator_config_path = __DIR__.'/../../dev-server/telegram_webhook_simulator.json';
$simulator_config = json_decode(file_get_contents($simulator_config_path), true);
$max_message_id = $simulator_config['max_message_id'];
if (!$max_message_id) {
    $max_message_id = 0;
}

sleep(1);

while (true) {
    $url = $telegram_api_url.'getUpdates?offset='.($max_message_id + 1).'&timeout=60';
    $api_ctx = stream_context_create(['http' => ['timeout' => 70]]);
    $api_resp = json_decode(file_get_contents($url, false, $api_ctx), true);
    if (!$api_resp || !$api_resp['ok']) {
        echo "API response was not OK. Waiting and retrying...\n";
        sleep(5);
        continue;
    }
    echo "API response (for offset ".($max_message_id + 1).") contains ".count($api_resp['result'])." results. Processing...\n";
    if (count($api_resp['result']) > 0) {
        $max_message_id = 0;
    }
    foreach ($api_resp['result'] as $key => $value) {
        echo "Processing message ".$value['update_id']."...\n";
        $max_message_id = max($value['update_id'], $max_message_id);
        $url = "http://{$server_domain}/_/api/index.php/onTelegram?authenticityCode={$authenticity_code}";
        $dev_ctx = stream_context_create([
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($value, JSON_PRETTY_PRINT),
                'timeout' => 30,
            ],
        ]);
        $dev_resp = json_decode(file_get_contents($url, false, $dev_ctx), true);
        // TODO: Action based on webhook response (currently not used)
    }
    $simulator_config['max_message_id'] = $max_message_id;
    file_put_contents($simulator_config_path, json_encode($simulator_config, JSON_PRETTY_PRINT));
}
