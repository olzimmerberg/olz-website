<?php

class GoogleFetcher {
    public function fetchTokenDataForCode($token_request_data) {
        $google_token_url = 'https://oauth2.googleapis.com/token';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $google_token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        $token_response = json_decode($token_result, true);
        curl_close($ch);

        return $token_response;
    }

    public function fetchUserData($userinfo_request_data, $token_response) {
        $google_userinfo_url = 'https://www.googleapis.com/oauth2/v1/userinfo';

        $token_type = $token_response['token_type'] ?? 'Basic';
        $access_token = $token_response['access_token'] ?? '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $google_userinfo_url.'?'.http_build_query($userinfo_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$token_type} {$access_token}",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_result = curl_exec($ch);
        $userinfo_response = json_decode($userinfo_result, true);
        curl_close($ch);

        return $userinfo_response;
    }

    public function fetchPeopleApiData($people_api_request_data, $token_response) {
        $google_token_url = 'https://people.googleapis.com/v1/people/me';

        $token_type = $token_response['token_type'] ?? 'Basic';
        $access_token = $token_response['access_token'] ?? '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $google_token_url.'?'.http_build_query($people_api_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$token_type} {$access_token}",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $people_api_result = curl_exec($ch);
        $people_api_response = json_decode($people_api_result, true);
        curl_close($ch);

        return $people_api_response;
    }
}
