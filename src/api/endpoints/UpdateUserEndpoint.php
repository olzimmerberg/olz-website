<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class UpdateUserEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateUserEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            'id' => new IntegerField([]),
            'firstName' => new StringField(['allow_empty' => false]),
            'lastName' => new StringField(['allow_empty' => false]),
            'username' => new StringField(['allow_empty' => false]),
            'email' => new StringField(['allow_empty' => false]),
            'phone' => new StringField(['allow_null' => true]),
            'gender' => new EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'birthdate' => new DateTimeField(['allow_null' => true]),
            'street' => new StringField(['allow_empty' => true]),
            'postalCode' => new StringField(['allow_empty' => true]),
            'city' => new StringField(['allow_empty' => true]),
            'region' => new StringField(['allow_empty' => true]),
            'countryCode' => new StringField(['max_length' => 2, 'allow_empty' => true]),
        ];
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'ERROR'];
        }

        $new_username = $input['username'];
        if (!$this->authUtils->isUsernameAllowed($new_username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }

        $user->setFirstName($input['firstName']);
        $user->setLastName($input['lastName']);
        $user->setUsername($new_username);
        $user->setEmail($input['email']);
        $user->setPhone($input['phone']);
        $user->setGender($input['gender']);
        $user->setBirthdate(new DateTime($input['birthdate']));
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $this->entityManager->flush();

        $this->session->set('user', $input['username']);

        return [
            'status' => 'OK',
        ];
    }
}
