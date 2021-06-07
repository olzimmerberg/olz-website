<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class UpdateUserEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateUserEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new IntegerField('id', []),
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
            new StringField('countryCode', ['max_length' => 2, 'allow_empty' => true]),
        ];
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'ERROR'];
        }

        $user->setFirstName($input['firstName']);
        $user->setLastName($input['lastName']);
        $user->setUsername($input['username']);
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
