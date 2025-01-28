<?php

namespace Olz\Users\Endpoints;

use Olz\Api\ApiObjects\IsoCountry;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDate;

/**
 * @phpstan-type OlzUserId int
 * @phpstan-type OlzUserInfoData array{
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 *   email?: ?non-empty-string,
 *   avatarImageId?: array<string, string>,
 * }
 */
trait UserInfoEndpointTrait {
    use WithUtilsTrait;

    public function configureUserEndpointTrait(): void {
        $this->phpStanUtils->registerApiObject(IsoDate::class);
        $this->phpStanUtils->registerApiObject(IsoCountry::class);
    }

    /** @return OlzUserInfoData */
    public function getEntityData(User $entity): array {
        $has_official_email = $this->authUtils()->hasPermission('user_email', $entity);
        $host = $this->envUtils()->getEmailForwardingHost();
        $email = $has_official_email
            ? "{$entity->getUsername()}@{$host}"
            : ($entity->getEmail() ? $entity->getEmail() : null);

        return [
            'firstName' => $entity->getFirstName(),
            'lastName' => $entity->getLastName(),
            'email' => $email ? $this->generalUtils()->base64EncodeUrl($email) : null,
            'avatarImageId' => $this->authUtils()->getUserAvatar($entity),
        ];
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
