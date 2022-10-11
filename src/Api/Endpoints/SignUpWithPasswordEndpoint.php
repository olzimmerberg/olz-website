<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\ValidationError;

class SignUpWithPasswordEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'SignUpWithPasswordEndpoint';
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
            'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
            'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
            'username' => new FieldTypes\StringField(['allow_empty' => false]),
            'password' => new FieldTypes\StringField(['allow_empty' => false]),
            'email' => new FieldTypes\StringField(['allow_empty' => false]),
            'phone' => new FieldTypes\StringField(['allow_null' => true]),
            'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'birthdate' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'street' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'postalCode' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'city' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'region' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'countryCode' => new FieldTypes\StringField(['max_length' => 2, 'allow_empty' => true, 'allow_null' => true]),
            'siCardNumber' => new FieldTypes\IntegerField(['min_value' => 100000, 'allow_empty' => true, 'allow_null' => true]),
            'solvNumber' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
        ]]);
    }

    protected function handle($input) {
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $first_name = $input['firstName'];
        $last_name = $input['lastName'];
        $username = $input['username'];
        $email = $input['email'];
        $this->log()->info("New sign-up (using password): {$first_name} {$last_name} ({$username})");
        if (!$this->authUtils()->isUsernameAllowed($username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        if (!$this->authUtils()->isPasswordAllowed($input['password'])) {
            throw new ValidationError(['password' => ["Das Passwort muss mindestens 8 Zeichen lang sein."]]);
        }
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);
        $user_repo = $this->entityManager()->getRepository(User::class);

        $same_username_user = $user_repo->findOneBy(['username' => $username]);
        $same_email_user = $user_repo->findOneBy(['email' => $email]);
        if ($same_username_user) {
            if ($same_username_user->getPasswordHash()) {
                throw new ValidationError(['username' => ["Es existiert bereits eine Person mit diesem Benutzernamen. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?"]]);
            }
            // If it's an existing user WITHOUT password, we just update that existing user!
            $user = $same_username_user;
        } elseif ($same_email_user) {
            if ($same_email_user->getPasswordHash()) {
                throw new ValidationError(['email' => ["Es existiert bereits eine Person mit dieser E-Mail Adresse. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?"]]);
            }
            // If it's an existing user WITHOUT password, we just update that existing user!
            $user = $same_email_user;
        } else {
            $user = new User();
        }

        $birthdate = $input['birthdate'] ? new \DateTime($input['birthdate']) : null;

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailIsVerified(false);
        $user->setEmailVerificationToken(null);
        $user->setPhone($input['phone']);
        $user->setPasswordHash(password_hash($input['password'], PASSWORD_DEFAULT));
        $user->setFirstName($first_name);
        $user->setLastName($last_name);
        $user->setGender($input['gender']);
        $user->setBirthdate($birthdate);
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $user->setSiCardNumber($input['siCardNumber']);
        $user->setSolvNumber($input['solvNumber']);
        $user->setPermissions('');
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
        $user->setCreatedAt($now_datetime);
        $user->setLastModifiedAt($now_datetime);
        $user->setLastLoginAt(null);

        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        $this->session()->set('auth', $user->getPermissions());
        $this->session()->set('root', $root);
        $this->session()->set('user', $user->getUsername());
        $this->session()->set('user_id', $user->getId());
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_PASSWORD', $user->getUsername());

        return ['status' => 'OK'];
    }
}
