<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\ApiObjects\IsoCountry;
use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * Note: `userId` can be of a managed user.
 *
 * @phpstan-type UserPrefillData array{
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 *   username: non-empty-string,
 *   email: non-empty-string,
 *   phone?: ?non-empty-string,
 *   gender?: ?('M'|'F'|'O'),
 *   birthdate?: ?IsoDate,
 *   street?: ?string,
 *   postalCode?: ?string,
 *   city?: ?string,
 *   region?: ?string,
 *   countryCode?: ?IsoCountry,
 *   siCardNumber?: ?int<100000, max>,
 *   solvNumber?: ?string,
 * }
 *
 * @extends TypedEndpoint<
 *   array{
 *     userId?: ?int<1, max>,
 *   },
 *   UserPrefillData,
 * >
 */
class GetPrefillValuesEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [IsoDate::class, IsoCountry::class];
    }

    public static function getIdent(): string {
        return 'GetPrefillValuesEndpoint';
    }

    protected function handle(mixed $input): mixed {
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
