<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\ValidationError;

require_once __DIR__.'/../OlzEndpoint.php';

class SignUpWithPasswordEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setEntityManager($entityManager);
    }

    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

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
        ]]);
    }

    protected function handle($input) {
        $first_name = $input['firstName'];
        $last_name = $input['lastName'];
        $username = $input['username'];
        $email = $input['email'];
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
        $user->setUsername($username);
        $user->setEmail($email);
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
