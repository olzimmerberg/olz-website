<?php

use Olz\Entity\AuthRequest;
use Olz\Entity\StravaLink;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class SignUpWithStravaEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'SignUpWithStravaEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'stravaUser' => new FieldTypes\StringField(['allow_empty' => false]),
            'accessToken' => new FieldTypes\StringField(['allow_empty' => false]),
            'refreshToken' => new FieldTypes\StringField(['allow_empty' => false]),
            'expiresAt' => new FieldTypes\DateTimeField(['allow_empty' => false]),
            'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
            'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
            'username' => new FieldTypes\StringField(['allow_empty' => false]),
            'email' => new FieldTypes\StringField(['allow_empty' => false]),
            'phone' => new FieldTypes\StringField(['allow_null' => true]),
            'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'birthdate' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'street' => new FieldTypes\StringField(['allow_empty' => true]),
            'postalCode' => new FieldTypes\StringField(['allow_empty' => true]),
            'city' => new FieldTypes\StringField(['allow_empty' => true]),
            'region' => new FieldTypes\StringField(['allow_empty' => true]),
            'countryCode' => new FieldTypes\StringField(['allow_empty' => true]),
            'siCardNumber' => new FieldTypes\IntegerField(['min_value' => 100000, 'allow_empty' => true, 'allow_null' => true]),
            'solvNumber' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
        ]]);
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
        $user->setSiCardNumber($input['siCardNumber']);
        $user->setSolvNumber($input['solvNumber']);
        $user->setZugriff('');
        $user->setRoot(null);
        $user->setMemberType(null);
        $user->setMemberLastPaid(null);
        $user->setWantsPostalMail(false);
        $user->setPostalTitle(null);
        $user->setPostalName(null);
        $user->setJoinedOn(null);
        $user->setJoinedReason(null);
        $user->setLeftOn(null);
        $user->setLeftReason(null);
        $user->setNotes('');

        $strava_link = new StravaLink();
        $strava_link->setStravaUser($input['stravaUser']);
        $strava_link->setAccessToken($input['accessToken']);
        $strava_link->setExpiresAt(new \DateTime($input['expiresAt']));
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
