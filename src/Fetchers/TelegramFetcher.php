<?php

namespace Olz\Fetchers;

class TelegramFetcher {
    public function callTelegramApi($command, $args, $bot_token) {
        $telegram_url = "https://api.telegram.org/bot{$bot_token}/{$command}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $telegram_result = curl_exec($ch);
        $telegram_response = json_decode($telegram_result, true);
        curl_close($ch);

        return $telegram_response;
    }
}
