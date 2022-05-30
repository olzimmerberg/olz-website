<?php

class StravaFetcher {
    public function fetchTokenDataForCode($token_request_data) {
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
