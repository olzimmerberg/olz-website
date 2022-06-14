<?php

namespace Olz\Fetchers;

class FacebookFetcher {
    public function fetchTokenDataForCode($token_request_data) {
        $facebook_token_url = 'https://graph.facebook.com/v8.0/oauth/access_token';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $facebook_token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        $token_response = json_decode($token_result, true);
        curl_close($ch);

        return $token_response;
    }

    public function fetchUserData($userinfo_request_data) {
        $facebook_userinfo_url = 'https://graph.facebook.com/v8.0/me';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $facebook_userinfo_url.'?'.http_build_query($userinfo_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_result = curl_exec($ch);
        $userinfo_response = json_decode($userinfo_result, true);
        curl_close($ch);

        return $userinfo_response;
    }
}
