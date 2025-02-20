<?php

namespace Olz\Users\Endpoints;

use Olz\Api\ApiObjects\IsoCountry;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;

/**
 * @phpstan-type OlzUserId int
 * @phpstan-type OlzUserData array{
 *   parentUserId?: ?int,
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 *   username: non-empty-string,
 *   password?: ?non-empty-string,
 *   email?: ?non-empty-string,
 *   phone?: ?non-empty-string,
 *   gender?: ?('M'|'F'|'O'),
 *   birthdate?: ?IsoDate,
 *   street?: ?non-empty-string,
 *   postalCode?: ?non-empty-string,
 *   city?: ?non-empty-string,
 *   region?: ?non-empty-string,
 *   countryCode?: ?IsoCountry,
 *   siCardNumber?: ?int<100000, max>,
 *   solvNumber?: ?non-empty-string,
 *   avatarImageId?: ?non-empty-string,
 * }
 */
trait UserEndpointTrait {
    use WithUtilsTrait;

    public function configureUserEndpointTrait(): void {
        $this->phpStanUtils->registerApiObject(IsoDate::class);
        $this->phpStanUtils->registerApiObject(IsoCountry::class);
    }

    /** @return OlzUserData */
    public function getEntityData(User $entity): array {
        return [
            'parentUserId' => $entity->getParentUserId(),
            'firstName' => $entity->getFirstName() ?: '-',
            'lastName' => $entity->getLastName() ?: '-',
            'username' => $entity->getUsername() ?: '-',
            'password' => null,
            'email' => $entity->getEmail() ? $entity->getEmail() : null,
            'phone' => $entity->getPhone() ? $entity->getPhone() : null,
            'gender' => $this->getGenderForApi($entity),
            'birthdate' => IsoDate::fromDateTime($entity->getBirthdate()),
            'street' => $entity->getStreet() ? $entity->getStreet() : null,
            'postalCode' => $entity->getPostalCode() ? $entity->getPostalCode() : null,
            'city' => $entity->getCity() ? $entity->getCity() : null,
            'region' => $entity->getRegion() ? $entity->getRegion() : null,
            'countryCode' => $entity->getCountryCode() ? IsoCountry::fromData($entity->getCountryCode()) : null,
            'siCardNumber' => $this->getSiCardNumberForApi($entity),
            'solvNumber' => $entity->getSolvNumber() ? $entity->getSolvNumber() : null,
            'avatarImageId' => $entity->getAvatarImageId() ? $entity->getAvatarImageId() : null,
        ];
    }

    /** @param OlzUserData $input_data */
    public function updateEntityWithData(User $entity, array $input_data): void {
        $birthdate = $input_data['birthdate'] ?? null;
        $valid_birthdate = $birthdate
            ? new \DateTime($birthdate->format('Y-m-d').' 12:00:00')
            : null;
        $avatar_image_id = $input_data['avatarImageId'] ?? null;
        $valid_avatar_image_id = $avatar_image_id
            ? $this->uploadUtils()->getValidUploadId($avatar_image_id)
            : null;
        $si_card_number = $input_data['siCardNumber'] ?? null;
        $valid_si_card_number = $si_card_number ? strval($si_card_number) : null;

        $entity->setParentUserId($input_data['parentUserId'] ?? null);
        $entity->setUsername($input_data['username']);
        $entity->setFirstName($input_data['firstName']);
        $entity->setLastName($input_data['lastName']);
        $entity->setEmail($input_data['email'] ?? null);
        $entity->setPhone($input_data['phone'] ?? null);
        $entity->setGender($input_data['gender'] ?? null);
        $entity->setBirthdate($valid_birthdate);
        $entity->setStreet($input_data['street'] ?? null);
        $entity->setPostalCode($input_data['postalCode'] ?? null);
        $entity->setCity($input_data['city'] ?? null);
        $entity->setRegion($input_data['region'] ?? null);
        $entity->setCountryCode($input_data['countryCode']?->data());
        $entity->setSiCardNumber($valid_si_card_number);
        $entity->setSolvNumber($input_data['solvNumber'] ?? null);
        $entity->setAvatarImageId($valid_avatar_image_id);
    }

    /** @param OlzUserData $input_data */
    public function persistUploads(User $entity, array $input_data): void {
        if ($entity->getAvatarImageId()) {
            $this->persistOlzImages($entity, [$entity->getAvatarImageId()]);
        }
    }

    public function editUploads(User $entity): void {
        if ($entity->getAvatarImageId()) {
            $this->editOlzImages($entity, [$entity->getAvatarImageId()]);
        }
    }

    protected function getEntityById(int $id): User {
        $repo = $this->entityManager()->getRepository(User::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
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
        if (!$string) {
            return null;
        }
        $number = intval($string);
        if ($number < 100000) {
            throw new \Exception("Invalid SI Card Number: {$string} ({$entity})");
        }
        return $number;
    }
}
