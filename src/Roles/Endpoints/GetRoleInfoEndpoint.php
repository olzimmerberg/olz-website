<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Roles\Role;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzRoleId int
 * @phpstan-type OlzRoleInfoData array{
 *   name?: ?non-empty-string,
 *   username?: ?non-empty-string,
 *   email?: ?array<non-empty-string>,
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
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['captchaToken'] ?? null;
        $is_valid_token = $token ? $this->captchaUtils()->validateToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Bot-Prüfung nicht bestanden!');
        }

        $id = $input['id'];
        $repo = $this->entityManager()->getRepository(Role::class);
        $role = $repo->findOneBy(['id' => $id]);
        if (!$role) {
            throw new HttpError(404, "Nicht gefunden.");
        }

        $host = $this->envUtils()->getEmailForwardingHost();
        $assignees = $role->getUsers();
        $assignee_infos = [];
        foreach ($assignees as $assignee) {
            $has_official_email = $this->authUtils()->hasPermission('user_email', $assignee);
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

        $has_role_email = $this->authUtils()->hasRolePermission('role_email', $role);
        $role_email = $has_role_email ? "{$role->getUsername()}@{$host}" : null;
        return [
            'name' => $role->getName() ? (html_entity_decode($role->getName()) ?: null) : null,
            'username' => $role->getUsername() ? $role->getUsername() : null,
            'email' => $this->emailUtils()->obfuscateEmail($role_email),
            'assignees' => $assignee_infos,
        ];
    }
}
