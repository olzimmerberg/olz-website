<?php

namespace Olz\Utils;

class StravaUtils {
    use WithUtilsTrait;
    public const UTILS = [];

    public static function fromEnv() {
        require_once __DIR__.'/../../_/config/paths.php';
        require_once __DIR__.'/../../_/config/server.php';
        require_once __DIR__.'/../../_/fetchers/StravaFetcher.php';

        $env_utils = EnvUtils::fromEnv();
        $base_href = $env_utils->getBaseHref();
        $code_href = $env_utils->getCodeHref();
        $redirect_url = $base_href.$code_href.'konto_strava.php';
        $strava_fetcher = new \StravaFetcher();

        $instance = new self();
        $instance->populateFromEnv(self::UTILS);
        $instance->setClientId($env_utils->getStravaClientId());
        $instance->setClientSecret($env_utils->getStravaClientSecret());
        $instance->setRedirectUrl($redirect_url);
        $instance->setStravaFetcher($strava_fetcher);
        return $instance;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

    public function setRedirectUrl($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function setStravaFetcher($strava_fetcher) {
        $this->strava_fetcher = $strava_fetcher;
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
