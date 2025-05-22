<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Captcha\Utils\CaptchaUtilsTrait;
use Olz\Entity\Roles\Role;
use Olz\Users\Endpoints\GetUserInfoEndpoint;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EmailUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EnvUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzRoleId int
 * @phpstan-type OlzRoleInfoData array{
 *   name?: ?non-empty-string,
 *   username?: ?non-empty-string,
 *   assignees: array<array{
 *     firstName: non-empty-string,
 *     lastName: non-empty-string,
 *     email?: ?array<non-empty-string>,
 *     avatarImageId?: array<string, string>,
 *   }>
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{id: OlzRoleId, captchaToken?: ?non-empty-string},
 *   OlzRoleInfoData
 * >
 */
class GetRoleInfoEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use CaptchaUtilsTrait;
    use EmailUtilsTrait;
    use EntityManagerTrait;
    use EnvUtilsTrait;

    public function configure(): void {
        $this->phpStanUtils->registerTypeImport(GetUserInfoEndpoint::class);
    }

    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['captchaToken'] ?? null;
        $is_valid_token = $token ? $this->captchaUtils()->validateToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Captcha token invalid');
        }

        $id = $input['id'];
        $repo = $this->entityManager()->getRepository(Role::class);
        $role = $repo->findOneBy(['id' => $id]);
        if (!$role) {
            throw new HttpError(404, "Nicht gefunden.");
        }

        $assignees = $role->getUsers();
        $assignee_infos = [];
        foreach ($assignees as $assignee) {
            $has_official_email = $this->authUtils()->hasPermission('user_email', $assignee);
            $host = $this->envUtils()->getEmailForwardingHost();
            $email = $has_official_email
                ? "{$assignee->getUsername()}@{$host}"
                : ($assignee->getEmail() ? $assignee->getEmail() : null);

            $assignee_infos[] = [
                'firstName' => $assignee->getFirstName() ?: '-',
                'lastName' => $assignee->getLastName() ?: '-',
                'email' => $this->emailUtils()->obfuscateEmail($email),
                'avatarImageId' => $this->authUtils()->getUserAvatar($assignee),
            ];
        }

        return [
            'name' => $role->getName() ? $role->getName() : null,
            'username' => $role->getUsername() ? $role->getUsername() : null,
            'assignees' => $assignee_infos,
        ];
    }
}
