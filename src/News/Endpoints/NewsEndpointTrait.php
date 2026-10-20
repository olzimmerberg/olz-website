<?php

namespace Olz\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDateTime;

/**
 * @phpstan-type OlzNewsId int
 * @phpstan-type OlzNewsData array{
 *   format: OlzNewsFormat,
 *   authorUserId?: ?int,
 *   authorRoleId?: ?int,
 *   authorName?: ?non-empty-string,
 *   authorEmail?: ?non-empty-string,
 *   publishAt?: ?IsoDateTime,
 *   title: non-empty-string,
 *   teaser: string,
 *   content: string,
 *   externalUrl?: ?non-empty-string,
 *   tags: array<non-empty-string>,
 *   terminId?: ?int,
 *   imageIds?: ?array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 * }
 * @phpstan-type OlzNewsFormat 'aktuell'|'kaderblog'|'forum'|'galerie'|'video'|'anonymous'
 */
trait NewsEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzNewsData */
    public function getEntityData(NewsEntry $entity): array {
        $author_name = $entity->getAuthorName();
        $author_email = $entity->getAuthorEmail();
        $published_date = $entity->getPublishedDate()->format('Y-m-d');
        $published_time = $entity->getPublishedTime()?->format('H:i:s') ?? '00:00:00';
        $tags_for_api = $this->getTagsForApi($entity->getTags());
        $external_url = $entity->getExternalUrl();

        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($entity->getImageIds());
        $file_ids = $entity->getStoredFileUploadIds();

        return [
            'format' => $this->getFormatForApi($entity),
            'authorUserId' => $entity->getAuthorUser()?->getId(),
            'authorRoleId' => $entity->getAuthorRole()?->getId(),
            'authorName' => $author_name ? $author_name : null,
            'authorEmail' => $author_email ? $author_email : null,
            'publishAt' => new IsoDateTime("{$published_date} {$published_time}"),
            'title' => $entity->getTitle() ?: '-',
            'teaser' => $entity->getTeaser() ?? '',
            'content' => $entity->getContent() ?? '',
            'externalUrl' => $external_url ? $external_url : null,
            'tags' => $tags_for_api,
            'terminId' => $entity->getTermin()?->getId(),
            'imageIds' => $valid_image_ids,
            'fileIds' => $file_ids,
        ];
    }

    /** @param OlzNewsData $input_data */
    public function updateEntityWithData(NewsEntry $entity, array $input_data): void {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $author_user_id = $input_data['authorUserId'] ?? null;
        $author_user = $current_user;
        if ($author_user_id) {
            $author_user = $user_repo->findOneBy(['id' => $author_user_id]);
        }

        $author_role_id = $input_data['authorRoleId'] ?? null;
        $author_role = null;
        if ($author_role_id) {
            $is_admin = $this->authUtils()->hasPermission('all');
            $is_authenticated_role = $this->authUtils()->isRoleIdAuthenticated($author_role_id);
            if (!$is_authenticated_role && !$is_admin) {
                throw new HttpError(403, "Kein Zugriff auf Autor-Rolle!");
            }
            $author_role = $role_repo->findOneBy(['id' => $author_role_id]);
        }

        $termin_id = $input_data['terminId'] ?? null;
        $termin = $termin_id
            ? $termin_repo->findOneBy(['id' => $termin_id])
            : null;

        $publish_at = $input_data['publishAt'] ?? $now;

        $tags_for_db = $this->getTagsForDb($input_data['tags']);
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds'] ?? null);

        $entity->setAuthorUser($author_user);
        $entity->setAuthorRole($author_role);
        $entity->setAuthorName($input_data['authorName'] ?? null);
        $entity->setAuthorEmail($input_data['authorEmail'] ?? null);
        $entity->setPublishedDate($publish_at);
        $entity->setPublishedTime($publish_at);
        $entity->setTitle($input_data['title']);
        $entity->setTeaser($input_data['teaser']);
        $entity->setContent($input_data['content']);
        $entity->setExternalUrl($input_data['externalUrl'] ?? null);
        $entity->setTags($tags_for_db);
        $entity->setImageIds($valid_image_ids);
        $entity->setTermin($termin);
        $entity->setCounter(0);
        $entity->setFormat($this->getFormat($input_data['format']));
        $entity->setNewsletter(true);
    }

    /** @param OlzNewsData $input_data */
    public function persistUploads(NewsEntry $entity, array $input_data): void {
        $this->persistOlzImages($entity, $entity->getImageIds());
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(NewsEntry $entity): void {
        $this->editOlzImages($entity, $entity->getImageIds());
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): NewsEntry {
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $entity = $news_repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }

    // ---

    protected function getFormat(string $format): string {
        if ($format === 'anonymous') {
            return 'forum';
        }
        return $format;
    }

    /** @return OlzNewsFormat */
    protected function getFormatForApi(NewsEntry $entity): string {
        switch ($entity->getFormat()) {
            case 'aktuell': return 'aktuell';
            case 'anonymous': return 'anonymous';
            case 'forum': return 'forum';
            case 'galerie': return 'galerie';
            case 'kaderblog': return 'kaderblog';
            case 'video': return 'video';
            default: throw new \Exception("Unknown news format: {$entity->getFormat()} ({$entity})");
        }
    }

    /** @param array<string> $tags */
    protected function getTagsForDb(?array $tags): string {
        return ' '.implode(' ', $tags ?? []).' ';
    }

    /** @return array<non-empty-string> */
    protected function getTagsForApi(?string $tags): array {
        $tags_string = $tags ?? '';
        $tags_for_api = [];
        foreach (explode(' ', $tags_string) as $tag) {
            $trimmed = trim($tag);
            if ($trimmed) {
                $tags_for_api[] = $trimmed;
            }
        }
        return $tags_for_api;
    }
}
