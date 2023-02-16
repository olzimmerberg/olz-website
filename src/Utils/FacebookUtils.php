<?php

namespace Olz\Utils;

use Olz\Fetchers\FacebookFetcher;

class FacebookUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'dateUtils',
    ];

    protected $app_id;
    protected $app_secret;
    protected $redirect_url;
    protected $facebook_fetcher;

    public static function fromEnv() {
        require_once __DIR__.'/../../_/config/paths.php';

        $env_utils = EnvUtils::fromEnv();
        $base_href = $env_utils->getBaseHref();
        $code_href = $env_utils->getCodeHref();
        $redirect_url = $base_href.$code_href.'konto_facebook.php';
        $facebook_fetcher = new FacebookFetcher();

        $instance = new self();
        $instance->setAppId($env_utils->getFacebookAppId());
        $instance->setAppSecret($env_utils->getFacebookAppSecret());
        $instance->setRedirectUrl($redirect_url);
        $instance->setFacebookFetcher($facebook_fetcher);
        return $instance;
    }

    public function setAppId($app_id) {
        $this->app_id = $app_id;
    }

    public function setAppSecret($app_secret) {
        $this->app_secret = $app_secret;
    }

    public function setRedirectUrl($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function setFacebookFetcher($facebook_fetcher) {
        $this->facebook_fetcher = $facebook_fetcher;
    }

    public function getAuthUrl() {
        $facebook_auth_url = 'https://www.facebook.com/v8.0/dialog/oauth';
        $facebook_scopes = [
            'email',
            'public_profile',
        ];
        $data = [
            'client_id' => $this->app_id,
            'redirect_uri' => $this->redirect_url,
            'response_type' => 'code',
            'scope' => implode(',', $facebook_scopes),
        ];
        return "{$facebook_auth_url}?".http_build_query($data, '', '&');
    }

    public function getTokenDataForCode($code) {
        $token_request_data = [
            'client_id' => $this->app_id,
            'client_secret' => $this->app_secret,
            'code' => $code,
            'redirect_uri' => $this->redirect_url,
        ];
        $token_response = $this->facebook_fetcher->fetchTokenDataForCode($token_request_data);

        $me_request_fields = [
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'picture{url}',
        ];
        $userinfo_request_data = [
            'fields' => implode(',', $me_request_fields),
            'access_token' => $token_response['access_token'],
        ];
        $userinfo_response = $this->facebook_fetcher->fetchUserData($userinfo_request_data, $token_response);

        $now_secs = strtotime($this->dateUtils()->getIsoNow());

        return [
            'token_type' => $token_response['token_type'],
            'expires_at' => $now_secs + intval($token_response['expires_in']),
            'refresh_token' => null,
            'access_token' => $token_response['access_token'],
            'user_identifier' => $userinfo_response['id'],
            'first_name' => $userinfo_response['first_name'],
            'last_name' => $userinfo_response['last_name'],
            'email' => $userinfo_response['email'],
            'verified_email' => true,
            'profile_picture_url' => $userinfo_response['picture']['data']['url'],
        ];
    }

    public function getUserData($token_data) {
        return $token_data;
    }
}
