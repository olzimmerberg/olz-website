<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\News\NewsEntry;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzNewsId int
 * @phpstan-type OlzAuthorInfoData array{
 *   roleName?: ?non-empty-string,
 *   roleUsername?: ?non-empty-string,
 *   firstName: non-empty-string,
 *   lastName: string,
 *   email?: ?array<non-empty-string>,
 *   avatarImageId?: ?array<string, string>,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{id: OlzNewsId, recaptchaToken?: ?non-empty-string},
 *   OlzAuthorInfoData
 * >
 */
class GetAuthorInfoEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['recaptchaToken'] ?? null;
        $is_valid_token = $token ? $this->recaptchaUtils()->validateRecaptchaToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Recaptcha token invalid');
        }

        $id = $input['id'];
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $id]);
        if (!$news_entry) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();

        $first_name = $author_name ? $author_name : '-';
        $last_name = '';
        $email = $author_email;
        $avatar = null;
        if ($author_user) {
            $first_name = $author_user->getFirstName();
            $last_name = $author_user->getLastName();
            $has_official_email = $this->authUtils()->hasPermission('user_email', $author_user);
            $host = $this->envUtils()->getEmailForwardingHost();
            $email = $has_official_email
                ? "{$author_user->getUsername()}@{$host}"
                : ($author_user->getEmail() ? $author_user->getEmail() : null);
            $avatar = $this->authUtils()->getUserAvatar($author_user);
        }

        return [
            'roleName' => $author_role?->getName() ?: null,
            'roleUsername' => $author_role?->getUsername() ?: null,
            'firstName' => $first_name ?: '-',
            'lastName' => $last_name,
            'email' => $this->emailUtils()->obfuscateEmail($email),
            'avatarImageId' => $avatar,
        ];
    }
}
