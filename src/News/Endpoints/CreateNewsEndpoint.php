<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class CreateNewsEndpoint extends OlzCreateEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'CreateNewsEndpoint';
    }

    protected function getCustomRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [
                'recaptchaToken' => new FieldTypes\StringField(['allow_null' => true]),
            ],
        ]);
    }

    protected function getStatusField() {
        return new FieldTypes\EnumField(['allowed_values' => [
            'OK',
            'DENIED',
            'ERROR',
        ]]);
    }

    protected function handle($input) {
        $input_data = $input['data'];
        $format = $input_data['format'];

        $has_access = $this->authUtils()->hasPermission('any');
        if ($format !== 'anonymous' && !$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $token = $input['custom']['recaptchaToken'] ?? null;
        $is_valid_token = $token ? $this->recaptchaUtils()->validateRecaptchaToken($token) : false;
        if ($format === 'anonymous' && !$is_valid_token) {
            return ['status' => 'DENIED', 'id' => null];
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getSessionUser();
        $data_path = $this->envUtils()->getDataPath();

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

        $tags_for_db = $this->getTagsForDb($input_data['tags']);

        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $news_entry = new NewsEntry();
        $this->entityUtils()->createOlzEntity($news_entry, $input['meta']);
        $news_entry->setAuthorUser($author_user);
        $news_entry->setAuthorRole($author_role);
        $news_entry->setAuthorName($input_data['authorName']);
        $news_entry->setAuthorEmail($input_data['authorEmail']);
        $news_entry->setDate($now);
        $news_entry->setTime($now);
        $news_entry->setTitle($input_data['title']);
        $news_entry->setTeaser($input_data['teaser']);
        $news_entry->setContent($input_data['content']);
        $news_entry->setExternalUrl($input_data['externalUrl']);
        $news_entry->setTags($tags_for_db);
        $news_entry->setImageIds($valid_image_ids);
        // TODO: Do not ignore
        $news_entry->setTermin(0);
        $news_entry->setCounter(0);
        $news_entry->setFormat($this->getFormat($format));
        $news_entry->setNewsletter(1);

        $this->entityManager()->persist($news_entry);
        $this->entityManager()->flush();

        $news_entry_id = $news_entry->getId();

        $news_entry_img_path = "{$data_path}img/news/{$news_entry_id}/";
        if (!is_dir("{$news_entry_img_path}img/")) {
            mkdir("{$news_entry_img_path}img/", 0777, true);
        }
        if (!is_dir("{$news_entry_img_path}thumb/")) {
            mkdir("{$news_entry_img_path}thumb/", 0777, true);
        }
        $this->uploadUtils()->moveUploads($valid_image_ids, "{$news_entry_img_path}img/");
        // TODO: Generate default thumbnails.

        $news_entry_files_path = "{$data_path}files/news/{$news_entry_id}/";
        if (!is_dir("{$news_entry_files_path}")) {
            mkdir("{$news_entry_files_path}", 0777, true);
        }
        $this->uploadUtils()->moveUploads($input_data['fileIds'], $news_entry_files_path);

        return [
            'status' => 'OK',
            'id' => $news_entry_id,
        ];
    }

    protected function getFormat($format) {
        if ($format === 'anonymous') {
            return 'forum';
        }
        return $format;
    }
}
