<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';

class LoginWithStravaEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setStravaUtils($strava_utils) {
        $this->stravaUtils = $strava_utils;
    }

    public static function getIdent() {
        return 'LoginWithStravaEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'NOT_REGISTERED',
                'INVALID_CODE',
                'AUTHENTICATED',
            ]]),
            'tokenType' => new StringField(['allow_null' => true]),
            'expiresAt' => new DateTimeField(['allow_null' => true]),
            'refreshToken' => new StringField(['allow_null' => true]),
            'accessToken' => new StringField(['allow_null' => true]),
            'userIdentifier' => new StringField(['allow_null' => true]),
            'firstName' => new StringField(['allow_null' => true]),
            'lastName' => new StringField(['allow_null' => true]),
            'gender' => new EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'city' => new StringField(['allow_null' => true]),
            'region' => new StringField(['allow_null' => true]),
            'country' => new StringField(['allow_null' => true]),
            'profilePictureUrl' => new StringField(['allow_null' => true]),
        ];
    }

    public function getRequestFields() {
        return [
            'code' => new StringField([]),
        ];
    }

    protected function handle($input) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);

        $token_data = $this->stravaUtils->getTokenDataForCode($input['code']);
        if (!$token_data) {
            return [
                'status' => 'INVALID_CODE',
            ];
        }
        $user_data = $this->stravaUtils->getUserData($token_data);
        if (!$user_data) {
            return [
                'status' => 'INVALID_CODE',
            ];
        }

        $strava_user = strval($user_data['user_identifier']);
        $strava_link_repo = $this->entityManager->getRepository(StravaLink::class);
        $strava_link = $strava_link_repo->findOneBy(['strava_user' => $strava_user]);

        if (!$strava_link) {
            return [
                'status' => 'NOT_REGISTERED',
                'tokenType' => $user_data['token_type'],
                'expiresAt' => date('Y-m-d H:i:s', $user_data['expires_at']),
                'refreshToken' => $user_data['refresh_token'],
                'accessToken' => $user_data['access_token'],
                'userIdentifier' => $strava_user,
                'firstName' => $user_data['first_name'],
                'lastName' => $user_data['last_name'],
                'gender' => $user_data['gender'],
                'city' => $user_data['city'],
                'region' => $user_data['region'],
                'country' => $user_data['country'],
                'profilePictureUrl' => $user_data['profile_picture_url'],
            ];
        }

        $user = $strava_link->getUser();
        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $user->getUsername());
        $this->session->set('user_id', $user->getId());
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_STRAVA', $user->getUsername());
        return [
            'status' => 'AUTHENTICATED',
        ];
    }
}
