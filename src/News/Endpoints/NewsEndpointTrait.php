<?php

namespace Olz\News\Endpoints;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait NewsEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzNewsDataOrNull' : 'OlzNewsData',
            'field_structure' => [
                'format' => new FieldTypes\EnumField([
                    'export_as' => 'OlzNewsFormat',
                    'allowed_values' => ['aktuell', 'kaderblog', 'forum', 'galerie', 'video', 'anonymous'],
                ]),
                'authorUserId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'authorRoleId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'authorName' => new FieldTypes\StringField(['allow_null' => true]),
                'authorEmail' => new FieldTypes\StringField(['allow_null' => true]),
                'publishAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'title' => new FieldTypes\StringField([]),
                'teaser' => new FieldTypes\StringField(['allow_empty' => true]),
                'content' => new FieldTypes\StringField(['allow_empty' => true]),
                'externalUrl' => new FieldTypes\StringField(['allow_null' => true]),
                'tags' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'terminId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                    'allow_null' => true,
                ]),
                'fileIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(NewsEntry $entity): array {
        $data_path = $this->envUtils()->getDataPath();

        $author_user = $entity->getAuthorUser();
        $author_role = $entity->getAuthorRole();
        $author_name = $entity->getAuthorName();
        $author_email = $entity->getAuthorEmail();
        $published_date = $entity->getPublishedDate()->format('Y-m-d');
        $published_time = $entity->getPublishedTime()?->format('H:i:s') ?? '00:00:00';
        $tags_for_api = $this->getTagsForApi($entity->getTags() ?? '');
        $external_url = $entity->getExternalUrl();
        $termin_id = $entity->getTermin();

        $file_ids = [];
        $news_entry_files_path = "{$data_path}files/news/{$entity->getId()}/";
        $files_path_entries = is_dir($news_entry_files_path)
            ? scandir($news_entry_files_path) : [];
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_ids[] = $file_id;
            }
        }

        return [
            'format' => $entity->getFormat(),
            'authorUserId' => $author_user ? $author_user->getId() : null,
            'authorRoleId' => $author_role ? $author_role->getId() : null,
            'authorName' => $author_name ? $author_name : null,
            'authorEmail' => $author_email ? $author_email : null,
            'publishAt' => "{$published_date} {$published_time}",
            'title' => $entity->getTitle(),
            'teaser' => $entity->getTeaser(),
            'content' => $entity->getContent(),
            'externalUrl' => $external_url ? $external_url : null,
            'tags' => $tags_for_api,
            'terminId' => $termin_id ? $termin_id : null,
            'imageIds' => $entity->getImageIds(),
            'fileIds' => $file_ids,
        ];
    }

    public function updateEntityWithData(NewsEntry $entity, array $input_data): void {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
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

        $publish_at = $input_data['publishAt'] ? new \DateTime($input_data['publishAt']) : $now;

        $tags_for_db = $this->getTagsForDb($input_data['tags']);
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $entity->setAuthorUser($author_user);
        $entity->setAuthorRole($author_role);
        $entity->setAuthorName($input_data['authorName']);
        $entity->setAuthorEmail($input_data['authorEmail']);
        $entity->setPublishedDate($publish_at);
        $entity->setPublishedTime($publish_at);
        $entity->setTitle($input_data['title']);
        $entity->setTeaser($input_data['teaser']);
        $entity->setContent($input_data['content']);
        $entity->setExternalUrl($input_data['externalUrl']);
        $entity->setTags($tags_for_db);
        $entity->setImageIds($valid_image_ids);
        // TODO: Do not ignore
        $entity->setTermin(0);
        $entity->setCounter(0);
        $entity->setFormat($this->getFormat($input_data['format']));
        $entity->setNewsletter(1);
    }

    public function persistUploads(NewsEntry $entity, array $input_data): void {
        $data_path = $this->envUtils()->getDataPath();

        $news_entry_id = $entity->getId();
        $valid_image_ids = $entity->getImageIds();

        $news_entry_img_path = "{$data_path}img/news/{$news_entry_id}/";
        if (!is_dir("{$news_entry_img_path}img/")) {
            mkdir("{$news_entry_img_path}img/", 0777, true);
        }
        if (!is_dir("{$news_entry_img_path}thumb/")) {
            mkdir("{$news_entry_img_path}thumb/", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($valid_image_ids, "{$news_entry_img_path}img/");
        // TODO: Generate default thumbnails.

        $news_entry_files_path = "{$data_path}files/news/{$news_entry_id}/";
        if (!is_dir("{$news_entry_files_path}")) {
            mkdir("{$news_entry_files_path}", 0777, true);
        }
        $this->uploadUtils()->overwriteUploads($input_data['fileIds'], $news_entry_files_path);
    }

    // ---

    protected function getFormat($format) {
        if ($format === 'anonymous') {
            return 'forum';
        }
        return $format;
    }

    protected function getTagsForDb($tags) {
        return ' '.implode(' ', $tags ?? []).' ';
    }

    protected function getTagsForApi($tags) {
        $tags_string = $tags ?? '';
        $tags_for_api = [];
        foreach (explode(' ', $tags_string) as $tag) {
            if (trim($tag) != '') {
                $tags_for_api[] = trim($tag);
            }
        }
        return $tags_for_api;
    }
}
