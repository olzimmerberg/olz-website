<?php

class FacebookUtils {
    private $app_id;
    private $app_secret;
    private $redirect_url;

    public function __construct($app_id, $app_secret, $redirect_url) {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->redirect_url = $redirect_url;
    }

    public function setAppId($app_id) {
        $this->app_id = $app_id;
    }

    public function setAppSecret($app_secret) {
        $this->app_secret = $app_secret;
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
        $ch = curl_init();

        $facebook_token_url = 'https://graph.facebook.com/v8.0/oauth/access_token';
        $token_request_data = [
            'client_id' => $this->app_id,
            'client_secret' => $this->app_secret,
            'code' => $code,
            'redirect_uri' => $this->redirect_url,
        ];
        curl_setopt($ch, CURLOPT_URL, $facebook_token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_result = curl_exec($ch);
        $token_response = json_decode($token_result, true);

        curl_reset($ch);

        $facebook_userinfo_url = 'https://graph.facebook.com/v8.0/me';
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
        curl_setopt($ch, CURLOPT_URL, $facebook_userinfo_url.'?'.http_build_query($userinfo_request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_result = curl_exec($ch);
        $userinfo_response = json_decode($userinfo_result, true);

        curl_close($ch);

        return [
            'token_type' => $token_response['token_type'],
            'expires_at' => time() + intval($token_response['expires_in']),
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

function getFacebookUtilsFromEnv() {
    global $base_href, $code_href, $FACEBOOK_APP_ID, $FACEBOOK_APP_SECRET;
    require_once __DIR__.'/../../config/paths.php';
    require_once __DIR__.'/../../config/server.php';

    $redirect_url = $base_href.$code_href.'konto_facebook.php';

    return new FacebookUtils($FACEBOOK_APP_ID, $FACEBOOK_APP_SECRET, $redirect_url);
}
