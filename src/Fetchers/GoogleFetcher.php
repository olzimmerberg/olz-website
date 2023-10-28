<?php

namespace Olz\Fetchers;

class GoogleFetcher {
    public function fetchRecaptchaVerification($siteverify_request_data) {
        $google_siteverify_url = 'https://www.google.com/recaptcha/api/siteverify';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $google_siteverify_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($siteverify_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $siteverify_result = curl_exec($ch);
        $siteverify_response = json_decode($siteverify_result, true);
        curl_close($ch);

        return $siteverify_response;
    }
}
