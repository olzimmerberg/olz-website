<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetPrefillValuesEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'GetPrefillValuesEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
            'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
            'username' => new FieldTypes\StringField(['allow_empty' => false]),
            'email' => new FieldTypes\StringField(['allow_empty' => false]),
            'phone' => new FieldTypes\StringField(['allow_null' => true]),
            'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'birthdate' => new FieldTypes\DateField(['allow_null' => true]),
            'street' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'postalCode' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'city' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'region' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'countryCode' => new FieldTypes\StringField(['max_length' => 2, 'allow_empty' => true, 'allow_null' => true]),
            'siCardNumber' => new FieldTypes\IntegerField(['min_value' => 100000, 'allow_empty' => true, 'allow_null' => true]),
            'solvNumber' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                // Can be a managed user
                'userId' => new FieldTypes\IntegerField(['min_value' => 1, 'allow_null' => true]),
            ],
        ]);
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $auth_user = $this->authUtils()->getCurrentUser();
        $user_id = $input['userId'] ?? null;
        if ($user_id) {
            $user_repo = $this->entityManager()->getRepository(User::class);
            $user = $user_repo->findOneBy(['id' => $user_id]);
            if (!$user || $user->getParentUserId() != $auth_user->getId()) {
                throw new HttpError(403, "Kein Zugriff!");
            }
        } else {
            $user = $auth_user;
        }

        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $username = $user->getUsername();
        $email = $user->getEmail();
        $phone = $user->getPhone();
        $gender = $user->getGender();
        $birthdate = $user->getBirthdate();
        $street = $user->getStreet();
        $postal_code = $user->getPostalCode();
        $city = $user->getCity();
        $region = $user->getRegion();
        $country_code = $user->getCountryCode();
        $si_card_number = intval($user->getSiCardNumber());
        $solv_number = $user->getSolvNumber();

        return [
            'firstName' => $first_name,
            'lastName' => $last_name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'birthdate' => $birthdate ? $birthdate->format('Y-m-d') : null,
            'street' => $street,
            'postalCode' => $postal_code,
            'city' => $city,
            'region' => $region,
            'countryCode' => $country_code,
            'siCardNumber' => $si_card_number ? $si_card_number : null,
            'solvNumber' => $solv_number,
        ];
    }
}
