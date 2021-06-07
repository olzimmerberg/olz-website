<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';

class SignUpWithStravaEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'SignUpWithStravaEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new StringField('stravaUser', ['allow_empty' => false]),
            new StringField('accessToken', ['allow_empty' => false]),
            new StringField('refreshToken', ['allow_empty' => false]),
            new DateTimeField('expiresAt', ['allow_empty' => false]),
            new StringField('firstName', ['allow_empty' => false]),
            new StringField('lastName', ['allow_empty' => false]),
            new StringField('username', ['allow_empty' => false]),
            new StringField('email', ['allow_empty' => false]),
            new StringField('phone', ['allow_null' => true]),
            new EnumField('gender', ['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            new DateTimeField('birthdate', ['allow_null' => true]),
            new StringField('street', ['allow_empty' => true]),
            new StringField('postalCode', ['allow_empty' => true]),
            new StringField('city', ['allow_empty' => true]),
            new StringField('region', ['allow_empty' => true]),
            new StringField('countryCode', ['allow_empty' => true]),
        ];
    }

    protected function handle($input) {
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);

        $user = new User();
        $user->setUsername($input['username']);
        $user->setEmail($input['email']);
        $user->setEmailIsVerified(false);
        $user->setEmailVerificationToken(null);
        $user->setPhone($input['phone']);
        $user->setPasswordHash('');
        $user->setFirstName($input['firstName']);
        $user->setLastName($input['lastName']);
        $user->setGender($input['gender']);
        $user->setBirthdate($input['birthdate']);
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $user->setZugriff('');
        $user->setRoot(null);

        $strava_link = new StravaLink();
        $strava_link->setStravaUser($input['stravaUser']);
        $strava_link->setAccessToken($input['accessToken']);
        $strava_link->setExpiresAt(new DateTime($input['expiresAt']));
        $strava_link->setRefreshToken($input['refreshToken']);
        $strava_link->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($strava_link);
        $this->entityManager->flush();

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $user->getUsername());
        $this->session->set('user_id', $user->getId());
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_STRAVA', $user->getUsername());

        return ['status' => 'OK'];
    }
}
