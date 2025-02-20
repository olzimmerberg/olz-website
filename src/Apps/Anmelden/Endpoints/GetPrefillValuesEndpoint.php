<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\ApiObjects\IsoCountry;
use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;

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
 * @extends OlzTypedEndpoint<
 *   array{
 *     userId?: ?int<1, max>,
 *   },
 *   UserPrefillData,
 * >
 */
class GetPrefillValuesEndpoint extends OlzTypedEndpoint {
    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerApiObject(IsoDate::class);
        $this->phpStanUtils->registerApiObject(IsoCountry::class);
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

        $first_name = $user->getFirstName() ?: '-';
        $last_name = $user->getLastName() ?: '-';
        $username = $user->getUsername() ?: '-';
        $email = $user->getEmail() ?: '-';
        $phone = $user->getPhone() ?: '-';
        $gender = $this->getGenderForApi($user);
        $birthdate = $user->getBirthdate();
        $street = $user->getStreet();
        $postal_code = $user->getPostalCode();
        $city = $user->getCity();
        $region = $user->getRegion();
        $country_code = $user->getCountryCode();
        $solv_number = $user->getSolvNumber();

        return [
            'firstName' => $first_name,
            'lastName' => $last_name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'birthdate' => IsoDate::fromDateTime($birthdate),
            'street' => $street,
            'postalCode' => $postal_code,
            'city' => $city,
            'region' => $region,
            'countryCode' => $country_code ? IsoCountry::fromData($country_code) : null,
            'siCardNumber' => $this->getSiCardNumberForApi($user),
            'solvNumber' => $solv_number,
        ];
    }

    // ---

    /** @return 'M'|'F'|'O'|null */
    protected function getGenderForApi(User $entity): ?string {
        switch ($entity->getGender()) {
            case 'M': return 'M';
            case 'F': return 'F';
            case 'O': return 'O';
            case null: return null;
            default: throw new \Exception("Unknown Gender: {$entity->getGender()} ({$entity})");
        }
    }

    /** @return ?int<100000, max> */
    protected function getSiCardNumberForApi(User $entity): ?int {
        $string = $entity->getSiCardNumber();
        if ($string === null) {
            return null;
        }
        $number = intval($string);
        if ($number < 100000) {
            throw new \Exception("Invalid SI Card Number: {$string} ({$entity})");
        }
        return $number;
    }
}
