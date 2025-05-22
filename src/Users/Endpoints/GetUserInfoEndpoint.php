<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Captcha\Utils\CaptchaUtilsTrait;
use Olz\Entity\Users\User;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EmailUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EnvUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzUserId int
 * @phpstan-type OlzUserInfoData array{
 *   firstName: non-empty-string,
 *   lastName: non-empty-string,
 *   email?: ?array<non-empty-string>,
 *   avatarImageId?: array<string, string>,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{id: OlzUserId, captchaToken?: ?non-empty-string},
 *   OlzUserInfoData
 * >
 */
class GetUserInfoEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use CaptchaUtilsTrait;
    use EmailUtilsTrait;
    use EntityManagerTrait;
    use EnvUtilsTrait;

    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['captchaToken'] ?? null;
        $is_valid_token = $token ? $this->captchaUtils()->validateToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Captcha token invalid');
        }

        $id = $input['id'];
        $repo = $this->entityManager()->getRepository(User::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }

        $has_official_email = $this->authUtils()->hasPermission('user_email', $entity);
        $host = $this->envUtils()->getEmailForwardingHost();
        $email = $has_official_email
            ? "{$entity->getUsername()}@{$host}"
            : ($entity->getEmail() ? $entity->getEmail() : null);

        return [
            'firstName' => $entity->getFirstName() ?: '-',
            'lastName' => $entity->getLastName() ?: '-',
            'email' => $this->emailUtils()->obfuscateEmail($email),
            'avatarImageId' => $this->authUtils()->getUserAvatar($entity),
        ];
    }
}
