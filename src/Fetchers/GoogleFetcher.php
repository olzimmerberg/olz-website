<?php

namespace Olz\Fetchers;

class GoogleFetcher {
    /**
     * @param array<string, mixed> $siteverify_request_data
     *
     * @return ?array<string, mixed>
     */
    public function fetchRecaptchaVerification(array $siteverify_request_data): ?array {
        $google_siteverify_url = 'https://www.google.com/recaptcha/api/siteverify';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $google_siteverify_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($siteverify_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $siteverify_result = curl_exec($ch);
        $siteverify_response = json_decode(!is_bool($siteverify_result) ? $siteverify_result : '', true);
        curl_close($ch);

        return $siteverify_response;
    }
}
