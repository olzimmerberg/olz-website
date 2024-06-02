<?php

namespace Olz\Fetchers;

class StravaFetcher {
    /**
     * @param array<string, mixed> $token_request_data
     *
     * @return ?array<string, mixed>
     */
    public function fetchTokenDataForCode(array $token_request_data): ?array {
        $strava_token_url = 'https://www.strava.com/api/v3/oauth/token';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strava_token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        $token_response = json_decode($token_result, true);
        curl_close($ch);

        return $token_response;
    }
}
