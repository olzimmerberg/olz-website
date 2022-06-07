<?php

use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\ValidationError;

require_once __DIR__.'/../OlzEndpoint.php';

class UpdateUserEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateUserEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField([]),
            'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
            'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
            'username' => new FieldTypes\StringField(['allow_empty' => false]),
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
            'avatarId' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
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

        $new_birthdate = $input['birthdate'] ? new \DateTime($input['birthdate']) : null;

        $now_datetime = new \DateTime($this->dateUtils->getIsoNow());
        $user->setFirstName($input['firstName']);
        $user->setLastName($input['lastName']);
        $user->setUsername($new_username);
        $user->setEmail($input['email']);
        $user->setPhone($input['phone']);
        $user->setGender($input['gender']);
        $user->setBirthdate($new_birthdate);
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $user->setSiCardNumber($input['siCardNumber']);
        $user->setSolvNumber($input['solvNumber']);
        $user->setLastModifiedAt($now_datetime);
        $this->entityManager->flush();

        $user_id = $user->getId();
        $data_path = $this->envUtils->getDataPath();
        $avatar_id = $input['avatarId'];
        $source_path = "{$data_path}temp/{$avatar_id}";
        $destination_path = "{$data_path}img/users/{$user_id}.jpg";
        if ($avatar_id === '-') {
            $this->unlink($destination_path);
        } elseif ($avatar_id) {
            $this->rename($source_path, $destination_path);
        }

        $this->session->set('user', $input['username']);

        return [
            'status' => 'OK',
        ];
    }

    protected function unlink($path) {
        unlink($path);
    }

    protected function rename($source_path, $destination_path) {
        rename($source_path, $destination_path);
    }
}
