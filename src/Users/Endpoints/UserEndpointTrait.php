<?php

namespace Olz\Users\Endpoints;

use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait UserEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzUserDataOrNull' : 'OlzUserData',
            'field_structure' => [
                'parentUserId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
                'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
                'username' => new FieldTypes\StringField(['allow_empty' => false]),
                // Password can be empty when creating a dependent family member, or not updating.
                'password' => new FieldTypes\StringField(['allow_null' => true]),
                // E-Mail can be empty when creating a dependent family member.
                'email' => new FieldTypes\StringField(['allow_null' => true]),
                'phone' => new FieldTypes\StringField(['allow_null' => true]),
                'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
                'birthdate' => new FieldTypes\DateField(['allow_null' => true]),
                'street' => new FieldTypes\StringField(['allow_null' => true]),
                'postalCode' => new FieldTypes\StringField(['allow_null' => true]),
                'city' => new FieldTypes\StringField(['allow_null' => true]),
                'region' => new FieldTypes\StringField(['allow_null' => true]),
                'countryCode' => new FieldTypes\StringField(['max_length' => 2, 'allow_null' => true]),
                'siCardNumber' => new FieldTypes\IntegerField(['min_value' => 100000, 'allow_null' => true]),
                'solvNumber' => new FieldTypes\StringField(['allow_null' => true]),
                'avatarImageId' => new FieldTypes\StringField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    /** @return array<string, mixed> */
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
            'birthdate' => $entity->getBirthdate()?->format('Y-m-d'),
            'street' => $entity->getStreet() ? $entity->getStreet() : null,
            'postalCode' => $entity->getPostalCode() ? $entity->getPostalCode() : null,
            'city' => $entity->getCity() ? $entity->getCity() : null,
            'region' => $entity->getRegion() ? $entity->getRegion() : null,
            'countryCode' => $entity->getCountryCode() ? $entity->getCountryCode() : null,
            'siCardNumber' => $entity->getSiCardNumber()
                ? intval($entity->getSiCardNumber())
                : null,
            'solvNumber' => $entity->getSolvNumber() ? $entity->getSolvNumber() : null,
            'avatarImageId' => $entity->getAvatarImageId() ? $entity->getAvatarImageId() : null,
        ];
    }

    /** @param array<string, mixed> $input_data */
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
            ? new \DateTime($input_data['birthdate'].' 12:00:00')
            : null);
        $entity->setStreet($input_data['street']);
        $entity->setPostalCode($input_data['postalCode']);
        $entity->setCity($input_data['city']);
        $entity->setRegion($input_data['region']);
        $entity->setCountryCode($input_data['countryCode']);
        $entity->setSiCardNumber($input_data['siCardNumber']);
        $entity->setSolvNumber($input_data['solvNumber']);
        $entity->setAvatarImageId($valid_avatar_image_id);
    }

    /** @param array<string, mixed> $input_data */
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
