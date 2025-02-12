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
            'firstName' => $entity->getFirstName(),
            'lastName' => $entity->getLastName(),
            'username' => $entity->getUsername(),
            'password' => null,
            'email' => $entity->getEmail() ? $entity->getEmail() : null,
            'phone' => $entity->getPhone() ? $entity->getPhone() : null,
            'gender' => $entity->getGender() ? $entity->getGender() : null,
            'birthdate' => IsoDate::fromDateTime($entity->getBirthdate()),
            'street' => $entity->getStreet() ? $entity->getStreet() : null,
            'postalCode' => $entity->getPostalCode() ? $entity->getPostalCode() : null,
            'city' => $entity->getCity() ? $entity->getCity() : null,
            'region' => $entity->getRegion() ? $entity->getRegion() : null,
            'countryCode' => $entity->getCountryCode() ? IsoCountry::fromData($entity->getCountryCode()) : null,
            'siCardNumber' => $entity->getSiCardNumber()
                ? intval($entity->getSiCardNumber())
                : null,
            'solvNumber' => $entity->getSolvNumber() ? $entity->getSolvNumber() : null,
            'avatarImageId' => $entity->getAvatarImageId() ? $entity->getAvatarImageId() : null,
        ];
    }

    /** @param OlzUserData $input_data */
    public function updateEntityWithData(User $entity, array $input_data): void {
        $valid_avatar_image_id = $input_data['avatarImageId']
            ? $this->uploadUtils()->getValidUploadId($input_data['avatarImageId'])
            : null;

        $entity->setParentUserId($input_data['parentUserId']);
        $entity->setUsername($input_data['username']);
        $entity->setFirstName($input_data['firstName']);
        $entity->setLastName($input_data['lastName']);
        $entity->setEmail($input_data['email']);
        $entity->setPhone($input_data['phone']);
        $entity->setGender($input_data['gender']);
        $entity->setBirthdate($input_data['birthdate']
            ? new \DateTime($input_data['birthdate']->format('Y-m-d').' 12:00:00')
            : null);
        $entity->setStreet($input_data['street']);
        $entity->setPostalCode($input_data['postalCode']);
        $entity->setCity($input_data['city']);
        $entity->setRegion($input_data['region']);
        $entity->setCountryCode($input_data['countryCode']?->data());
        $entity->setSiCardNumber(strval($input_data['siCardNumber']));
        $entity->setSolvNumber($input_data['solvNumber']);
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
}
