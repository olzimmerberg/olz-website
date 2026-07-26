<?php

namespace Olz\Apps\Members\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Apps\Members\Utils\MembersUtilsTrait;
use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzMemberInfo array{
 *   ident: non-empty-string,
 *   action: 'CREATE'|'UPDATE'|'DELETE'|'KEEP',
 *   username?: ?string,
 *   firstName?: ?string,
 *   lastName?: ?string,
 *   company?: ?string,
 *   matchingUsername?: ?non-empty-string,
 *   user?: ?array{
 *     id: int,
 *     firstName: non-empty-string,
 *     lastName: non-empty-string,
 *   },
 *   updates: array<non-empty-string, array{old: string, new: string}>,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{csvFileId: non-empty-string},
 *   array{status: 'OK'|'ERROR', members: array<OlzMemberInfo>}
 * >
 */
class ImportMembersEndpoint extends OlzTypedEndpoint {
    use MembersUtilsTrait;

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('vorstand')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Members import by {$user?->getUsername()}.");

        $member_info_by_ident = [];
        $csv_content = $this->getCsvContent($input['csvFileId']);
        $csv_members = $this->membersUtils()->parseCsv($csv_content);
        $member_repo = $this->entityManager()->getRepository(Member::class);
        $user_repo = $this->entityManager()->getRepository(User::class);

        $existing_member_is_deleted = [];
        foreach ($member_repo->getAllIdents() as $existing_member_ident) {
            // Assume deleted unless set to false later...
            $existing_member_is_deleted[$existing_member_ident] = true;
        }
        foreach ($csv_members as $csv_member) {
            $csv_member_ident = $this->membersUtils()->getMemberIdent($csv_member);
            $csv_member_username = $this->membersUtils()->getMemberUsername($csv_member);
            $csv_member_first_name = $this->membersUtils()->getMemberFirstName($csv_member);
            $csv_member_last_name = $this->membersUtils()->getMemberLastName($csv_member);
            $csv_member_company = $this->membersUtils()->getMemberCompany($csv_member);
            $enc_csv_member = json_encode($csv_member);
            $this->generalUtils()->checkNotFalse($enc_csv_member, "JSON encode failed");
            if (!$csv_member_ident) {
                $this->log()->warning("Member has no ident: {$enc_csv_member}");
                continue;
            }
            $existing_member_is_deleted[$csv_member_ident] = false;
            $user = $csv_member_username ? (
                $user_repo->findOneBy(['username' => $csv_member_username])
                ?? $user_repo->findOneBy(['old_username' => $csv_member_username])
            ) : null;
            $matching_user = $user_repo->findUserFuzzilyByName(
                trim($csv_member_first_name ?? ''),
                trim($csv_member_last_name ?? '')
            );
            $base_info = [
                'username' => $csv_member_username,
                'firstName' => $csv_member_first_name,
                'lastName' => $csv_member_last_name,
                'company' => $csv_member_company,
                'matchingUsername' => $matching_user?->getUsername(),
                'user' => $this->getUserData($user),
            ];
            $entity = $member_repo->findOneBy(['ident' => $csv_member_ident]);
            if (!$entity) {
                $member_info_by_ident[$csv_member_ident] = [...$base_info, 'action' => 'CREATE'];
                $entity = new Member();
                $this->entityUtils()->createOlzEntity($entity, ['onOff' => true]);
                $entity->setIdent($csv_member_ident);
                $entity->setUser($user);
                $entity->setData($enc_csv_member);
                $entity->setUpdates(null);
                $this->membersUtils()->update($entity, $user);
                $this->entityManager()->persist($entity);
            } else {
                if ($entity->getData() === $enc_csv_member && $entity->getUser() === $user) {
                    $member_info_by_ident[$csv_member_ident] = [...$base_info, 'action' => 'KEEP'];
                    $this->membersUtils()->update($entity, $user);
                } else {
                    $member_info_by_ident[$csv_member_ident] = [...$base_info, 'action' => 'UPDATE'];
                    $this->entityUtils()->updateOlzEntity($entity, []);
                    $entity->setUser($user);
                    $entity->setData($enc_csv_member);
                    $this->membersUtils()->update($entity, $user);
                }
            }
            $new_value_by_key = json_decode($entity->getUpdates() ?? '[]', true) ?: [];
            $updates = [];
            foreach ($new_value_by_key as $key => $new_value) {
                $updates[$key] = ['old' => $csv_member[$key] ?? '', 'new' => $new_value];
            }
            $member_info_by_ident[$csv_member_ident]['updates'] = $updates;
        }
        foreach ($existing_member_is_deleted as $int_ident => $is_deleted) {
            $csv_member_ident = "{$int_ident}";
            if ($is_deleted) {
                $member = $member_repo->findOneBy(['ident' => $csv_member_ident]);
                $member_info_by_ident[$csv_member_ident] = [
                    'action' => 'DELETE',
                    'username' => null,
                    'matchingUsername' => null,
                    'user' => $this->getUserData($member?->getUser()),
                    'updates' => [],
                ];
                if ($member) {
                    $this->entityManager()->remove($member);
                } else {
                    $this->log()->warning("Cannot delete inexistent member: {$csv_member_ident}");
                }
            }
        }
        $this->entityManager()->flush();

        $members = [];
        foreach ($member_info_by_ident as $int_ident => $member) {
            $member_ident = "{$int_ident}";
            $enc_member = json_encode($member);
            $this->generalUtils()->checkNotEmpty($member_ident, "Member ident must not be empty: {$enc_member}");
            $this->generalUtils()->checkNotEmpty($member['matchingUsername'], "Member matchingUsername must not be empty: {$enc_member}");
            $members[] = [
                'ident' => "{$member_ident}",
                'action' => $member['action'],
                'username' => $member['username'] ?? null,
                'firstName' => $member['firstName'] ?? null,
                'lastName' => $member['lastName'] ?? null,
                'company' => $member['company'] ?? null,
                'matchingUsername' => $member['matchingUsername'] ?? null,
                'user' => $member['user'] ?? null,
                'updates' => $member['updates'],
            ];
        }
        return ['status' => 'OK', 'members' => $members];
    }

    protected function getCsvContent(string $upload_id): string {
        $data_path = $this->envUtils()->getDataPath();
        $upload_path = "{$data_path}temp/{$upload_id}";
        if (!is_file($upload_path)) {
            throw new HttpError(400, 'Uploaded file not found!');
        }
        $csv_content = file_get_contents($upload_path);
        unlink($upload_path);
        $this->generalUtils()->checkNotFalse($csv_content, "Could not read uploaded Members CSV");
        return $csv_content;
    }

    /** @return ?array{
     *     id: int,
     *     firstName: non-empty-string,
     *     lastName: non-empty-string,
     *   }
     */
    protected function getUserData(?User $user): ?array {
        $user_id = $user?->getId();
        if (!$user_id) {
            return null;
        }
        return [
            'id' => $user_id,
            'firstName' => $user->getFirstName() ?: '-',
            'lastName' => $user->getLastName() ?: '-',
        ];
    }
}
