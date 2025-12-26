<?php

namespace Olz\Fetchers;

class TelegramFetcher {
    /**
     * @param array<string, mixed> $args
     *
     * @return ?array<string, mixed>
     */
    public function callTelegramApi(string $command, array $args, string $bot_token): ?array {
        $telegram_url = "https://api.telegram.org/bot{$bot_token}/{$command}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegram_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $telegram_result = curl_exec($ch);
        return json_decode(!is_bool($telegram_result) ? $telegram_result : '', true);
    }
}
