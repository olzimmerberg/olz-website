<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Entity\StravaLink;
use PhpTypeScriptApi\Fields\FieldTypes;

class LoginWithStravaEndpoint extends OlzEndpoint {
    protected const NULL_RESPONSE = [
        'tokenType' => null,
        'expiresAt' => null,
        'refreshToken' => null,
        'accessToken' => null,
        'userIdentifier' => null,
        'firstName' => null,
        'lastName' => null,
        'gender' => null,
        'city' => null,
        'region' => null,
        'country' => null,
        'profilePictureUrl' => null,
    ];

    public static function getIdent(): string {
        return 'LoginWithStravaEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'NOT_REGISTERED',
                'INVALID_CODE',
                'AUTHENTICATED',
            ]]),
            'tokenType' => new FieldTypes\StringField(['allow_null' => true]),
            'expiresAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'refreshToken' => new FieldTypes\StringField(['allow_null' => true]),
            'accessToken' => new FieldTypes\StringField(['allow_null' => true]),
            'userIdentifier' => new FieldTypes\StringField(['allow_null' => true]),
            'firstName' => new FieldTypes\StringField(['allow_null' => true]),
            'lastName' => new FieldTypes\StringField(['allow_null' => true]),
            'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'city' => new FieldTypes\StringField(['allow_null' => true]),
            'region' => new FieldTypes\StringField(['allow_null' => true]),
            'country' => new FieldTypes\StringField(['allow_null' => true]),
            'profilePictureUrl' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'code' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle(mixed $input): mixed {
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);

        $token_data = $this->stravaUtils()->getTokenDataForCode($input['code']);
        if (!$token_data) {
            return array_merge(self::NULL_RESPONSE, [
                'status' => 'INVALID_CODE',
            ]);
        }
        $user_data = $this->stravaUtils()->getUserData($token_data);
        if (!$user_data) {
            return array_merge(self::NULL_RESPONSE, [
                'status' => 'INVALID_CODE',
            ]);
        }

        $strava_user = strval($user_data['user_identifier']);
        $strava_link_repo = $this->entityManager()->getRepository(StravaLink::class);
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
        $this->session()->set('auth', $user->getPermissions());
        $this->session()->set('root', $root);
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_id', $user->getId());
        $this->session()->set('auth_user', $user->getUsername());
        $this->session()->set('auth_user_id', $user->getId());
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_STRAVA', $user->getUsername());
        return array_merge(self::NULL_RESPONSE, [
            'status' => 'AUTHENTICATED',
        ]);
    }
}
