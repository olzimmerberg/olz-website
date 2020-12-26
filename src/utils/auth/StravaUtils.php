<?php

class StravaUtils {
    private $client_id;
    private $client_secret;
    private $redirect_url;
    private $strava_fetcher;

    public function __construct($client_id, $client_secret, $redirect_url, $strava_fetcher) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_url = $redirect_url;
        $this->strava_fetcher = $strava_fetcher;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

    public function getAuthUrl() {
        $strava_auth_url = 'https://www.strava.com/oauth/authorize';
        $data = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_url,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'profile:read_all',
        ];
        return "{$strava_auth_url}?".http_build_query($data);
    }

    public function getTokenDataForCode($code) {
        $token_request_data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $token_response = $this->strava_fetcher->fetchTokenDataForCode($token_request_data);

        if (!isset($token_response['token_type'])) {
            return null;
        }

        return [
            'token_type' => $token_response['token_type'],
            'expires_at' => $token_response['expires_at'],
            'refresh_token' => $token_response['refresh_token'],
            'access_token' => $token_response['access_token'],
            'user_identifier' => $token_response['athlete']['id'],
            'first_name' => $token_response['athlete']['firstname'],
            'last_name' => $token_response['athlete']['lastname'],
            'gender' => $token_response['athlete']['sex'],
            'city' => $token_response['athlete']['city'],
            'region' => $token_response['athlete']['state'],
            'country' => $token_response['athlete']['country'],
            'profile_picture_url' => $token_response['athlete']['profile'],
        ];
    }

    public function getUserData($token_data) {
        return $token_data;
    }
}

function getStravaUtilsFromEnv() {
    global $base_href, $code_href, $_CONFIG;
    require_once __DIR__.'/../../config/paths.php';
    require_once __DIR__.'/../../config/server.php';
    require_once __DIR__.'/../../fetchers/StravaFetcher.php';

    $redirect_url = $base_href.$code_href.'konto_strava.php';
    $strava_fetcher = new StravaFetcher();

    return new StravaUtils(
        $_CONFIG->getStravaClientId(),
        $_CONFIG->getStravaClientSecret(),
        $redirect_url,
        $strava_fetcher
    );
}
