<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../utils/ValidationError.php';

class SignUpWithPasswordEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'SignUpWithPasswordEndpoint';
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
            new StringField('firstName', ['allow_empty' => false]),
            new StringField('lastName', ['allow_empty' => false]),
            new StringField('username', ['allow_empty' => false]),
            new StringField('password', ['allow_empty' => false]),
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
        $first_name = $input['firstName'];
        $last_name = $input['lastName'];
        $username = $input['username'];
        $this->logger->info("New sign-up (using password): {$first_name} {$last_name} ({$username})");
        if (!$this->authUtils->isUsernameAllowed($username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        if (!$this->authUtils->isPasswordAllowed($input['password'])) {
            throw new ValidationError(['password' => ["Das Passwort muss mindestens 8 Zeichen lang sein."]]);
        }
        $ip_address = $this->server['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager->getRepository(AuthRequest::class);
        $user_repo = $this->entityManager->getRepository(User::class);

        $user = $user_repo->findOneBy(['username' => $username]);
        if ($user) {
            if ($user->getPasswordHash()) {
                throw new ValidationError(['username' => ["Es existiert bereits eine Person mit diesem Benutzernamen."]]);
            }
            // If it's an existing user WITHOUT password, we just update that existing user!
        } else {
            $user = new User();
        }
        $user->setUsername($username);
        $user->setEmail($input['email']);
        $user->setEmailIsVerified(false);
        $user->setEmailVerificationToken(null);
        $user->setPhone($input['phone']);
        $user->setPasswordHash(password_hash($input['password'], PASSWORD_DEFAULT));
        $user->setFirstName($first_name);
        $user->setLastName($last_name);
        $user->setGender($input['gender']);
        $user->setBirthdate(new DateTime($input['birthdate']));
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $user->setZugriff('');
        $user->setRoot(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $root = $user->getRoot() !== '' ? $user->getRoot() : './';
        // Mögliche Werte für 'zugriff': all, ftp, termine, mail
        $this->session->set('auth', $user->getZugriff());
        $this->session->set('root', $root);
        $this->session->set('user', $user->getUsername());
        $this->session->set('user_id', $user->getId());
        $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_PASSWORD', $user->getUsername());

        return ['status' => 'OK'];
    }
}
